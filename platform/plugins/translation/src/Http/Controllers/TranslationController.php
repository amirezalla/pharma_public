<?php

namespace Botble\Translation\Http\Controllers;

use Assets;
use BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Language;
use Botble\Base\Supports\PclZip as Zip;
use Botble\Translation\Http\Requests\LocaleRequest;
use Botble\Translation\Http\Requests\TranslationRequest;
use Botble\Translation\Manager;
use Botble\Translation\Models\Translation;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RvMedia;
use Theme;
use ZipArchive;

class TranslationController extends BaseController
{
    protected Manager $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function getIndex(Request $request)
    {
        page_title()->setTitle(trans('plugins/translation::translation.translations'));

        Assets::addScripts(['bootstrap-editable'])
            ->addStyles(['bootstrap-editable'])
            ->addScriptsDirectly('vendor/core/plugins/translation/js/translation.js')
            ->addStylesDirectly('vendor/core/plugins/translation/css/translation.css');

        $group = $request->input('group');

        $locales = $this->loadLocales();
        $groups = Translation::groupBy('group');
        $excludedGroups = $this->manager->getConfig('exclude_groups');
        if ($excludedGroups) {
            $groups->whereNotIn('group', $excludedGroups);
        }

        $groups = $groups->select('group')->get()->pluck('group', 'group');
        if ($groups instanceof Collection) {
            $groups = $groups->all();
        }
        $groups = ['' => trans('plugins/translation::translation.choose_a_group')] + $groups;
        $numChanged = Translation::where('group', $group)->where('status', Translation::STATUS_CHANGED)->count();

        $allTranslations = Translation::where('group', $group)->orderBy('key')->get();
        $numTranslations = count($allTranslations);
        $translations = [];
        foreach ($allTranslations as $translation) {
            $translations[$translation->key][$translation->locale] = $translation;
        }

        return view('plugins/translation::index')
            ->with('translations', $translations)
            ->with('locales', $locales)
            ->with('groups', $groups)
            ->with('group', $group)
            ->with('numTranslations', $numTranslations)
            ->with('numChanged', $numChanged)
            ->with('editUrl', route('translations.group.edit', ['group' => $group]));
    }

    protected function loadLocales(): array
    {
        // Set the default locale as the first one.
        $locales = Translation::groupBy('locale')
            ->select('locale')
            ->get()
            ->pluck('locale');

        if ($locales instanceof Collection) {
            $locales = $locales->all();
        }
        $locales = array_merge([config('app.locale')], $locales);

        return array_unique($locales);
    }

    public function update(TranslationRequest $request, BaseHttpResponse $response)
    {
        $group = $request->input('group');

        if (! in_array($group, $this->manager->getConfig('exclude_groups'))) {
            $name = $request->input('name');
            $value = $request->input('value');

            [$locale, $key] = explode('|', $name, 2);
            $translation = Translation::firstOrNew([
                'locale' => $locale,
                'group' => $group,
                'key' => $key,
            ]);
            $translation->value = (string)$value ?: null;
            $translation->status = Translation::STATUS_CHANGED;
            $translation->save();
        }

        return $response;
    }

    public function postImport(Request $request, BaseHttpResponse $response)
    {
        $counter = $this->manager->importTranslations($request->input('replace', false));

        return $response->setMessage(trans('plugins/translation::translation.import_done', compact('counter')));
    }

    public function postPublish(Request $request, BaseHttpResponse $response)
    {
        if (! File::isWritable(lang_path()) || ! File::isWritable(lang_path('vendor'))) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/translation::translation.folder_is_not_writeable', ['lang_path' => lang_path()]));
        }

        $group = $request->input('group');

        $this->manager->exportTranslations($group);

        return $response->setMessage(trans('plugins/translation::translation.done_publishing'));
    }

    public function getLocales()
    {
        page_title()->setTitle(trans('plugins/translation::translation.locales'));

        Assets::addScriptsDirectly('vendor/core/plugins/translation/js/locales.js');

        $existingLocales = Language::getAvailableLocales();
        $languages = Language::getListLanguages();
        $flags = Language::getListLanguageFlags();

        $locales = collect($languages)->pluck(2, 0)->unique()->all();

        return view('plugins/translation::locales', compact('existingLocales', 'locales', 'flags'));
    }

    public function postLocales(LocaleRequest $request, BaseHttpResponse $response)
    {
        if (! File::isWritable(lang_path()) || ! File::isWritable(lang_path('vendor'))) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/translation::translation.folder_is_not_writeable', ['lang_path' => lang_path()]));
        }

        $locale = $request->input('locale');

        if (! File::isDirectory(lang_path($locale))) {
            $result = app(Manager::class)->downloadRemoteLocale($locale);

            if ($result['error']) {
                $defaultLocale = lang_path('en');
                if (File::exists($defaultLocale)) {
                    File::copyDirectory($defaultLocale, lang_path($locale));
                }

                $this->createLocaleInPath(lang_path('vendor/core'), $locale);
                $this->createLocaleInPath(lang_path('vendor/packages'), $locale);
                $this->createLocaleInPath(lang_path('vendor/plugins'), $locale);

                $themeLocale = Arr::first(BaseHelper::scanFolder(theme_path(Theme::getThemeName() . '/lang')));

                if ($themeLocale) {
                    File::copy(
                        theme_path(Theme::getThemeName() . '/lang/' . $themeLocale),
                        lang_path($locale . '.json')
                    );
                }
            }
        }

        return $response->setMessage(trans('core/base::notices.create_success_message'));
    }

    protected function createLocaleInPath(string $path, string $locale): int
    {
        $folders = File::directories($path);

        foreach ($folders as $module) {
            foreach (File::directories($module) as $item) {
                if (File::name($item) == 'en') {
                    File::copyDirectory($item, $module . '/' . $locale);
                }
            }
        }

        return count($folders);
    }

    public function deleteLocale($locale, BaseHttpResponse $response)
    {
        if ($locale !== 'en') {
            if (! File::isWritable(lang_path()) || ! File::isWritable(lang_path('vendor'))) {
                return $response
                    ->setError()
                    ->setMessage(trans('plugins/translation::translation.folder_is_not_writeable', ['lang_path' => lang_path()]));
            }

            $defaultLocale = lang_path($locale);
            if (File::exists($defaultLocale)) {
                File::deleteDirectory($defaultLocale);
            }

            if (File::exists(lang_path($locale . '.json'))) {
                File::delete(lang_path($locale . '.json'));
            }

            $this->removeLocaleInPath(lang_path('vendor/core'), $locale);
            $this->removeLocaleInPath(lang_path('vendor/packages'), $locale);
            $this->removeLocaleInPath(lang_path('vendor/plugins'), $locale);

            DB::table('translations')->where('locale', $locale)->delete();
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    protected function removeLocaleInPath(string $path, string $locale): int
    {
        $folders = File::directories($path);

        foreach ($folders as $module) {
            foreach (File::directories($module) as $item) {
                if (File::name($item) == $locale) {
                    File::deleteDirectory($item);
                }
            }
        }

        return count($folders);
    }

    public function getThemeTranslations(Request $request)
    {
        page_title()->setTitle(trans('plugins/translation::translation.theme-translations'));

        Assets::addScripts(['bootstrap-editable'])
            ->addStyles(['bootstrap-editable'])
            ->addScriptsDirectly('vendor/core/plugins/translation/js/theme-translations.js')
            ->addStylesDirectly('vendor/core/plugins/translation/css/theme-translations.css');

        $groups = Language::getAvailableLocales();
        $defaultLanguage = Arr::get($groups, 'en');

        if (! $request->has('ref_lang')) {
            $group = $defaultLanguage;
        } else {
            $group = Arr::first(Arr::where($groups, function ($item) use ($request) {
                return $item['locale'] == $request->input('ref_lang');
            }));
        }

        $translations = [];
        if ($group) {
            $jsonFile = lang_path($group['locale'] . '.json');

            if (! File::exists($jsonFile)) {
                $jsonFile = theme_path(Theme::getThemeName() . '/lang/' . $group['locale'] . '.json');
            }

            if (! File::exists($jsonFile)) {
                $languages = BaseHelper::scanFolder(theme_path(Theme::getThemeName() . '/lang'));

                if (! empty($languages)) {
                    $jsonFile = theme_path(Theme::getThemeName() . '/lang/' . Arr::first($languages));
                }
            }

            if (File::exists($jsonFile)) {
                $translations = BaseHelper::getFileData($jsonFile);
            }

            if ($group['locale'] != 'en') {
                $defaultEnglishFile = theme_path(Theme::getThemeName() . '/lang/enold.json');

                if ($defaultEnglishFile) {
                    $enTranslations = BaseHelper::getFileData($defaultEnglishFile);
                    $translations = array_merge($enTranslations, $translations);

                    $enTranslationKeys = array_keys($enTranslations);

                    foreach ($translations as $key => $translation) {
                        if (! in_array($key, $enTranslationKeys)) {
                            Arr::forget($translations, $key);
                        }
                    }
                }
            }
        }

        ksort($translations);

        return view(
            'plugins/translation::theme-translations',
            compact('translations', 'groups', 'group', 'defaultLanguage')
        );
    }

    public function postThemeTranslations(Request $request, BaseHttpResponse $response)
    {
        if (! File::isWritable(lang_path())) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/translation::translation.folder_is_not_writeable', ['lang_path' => lang_path()]));
        }

        $locale = $request->input('pk');

        if ($locale) {
            $translations = [];

            $jsonFile = lang_path($locale . '.json');

            if (! File::exists($jsonFile)) {
                $jsonFile = theme_path(Theme::getThemeName() . '/lang/' . $locale . '.json');
            }

            if (File::exists($jsonFile)) {
                $translations = BaseHelper::getFileData($jsonFile);
            }

            if ($locale != 'en') {
                $defaultEnglishFile = theme_path(Theme::getThemeName() . '/lang/enold.json');

                if ($defaultEnglishFile) {
                    $enTranslations = BaseHelper::getFileData($defaultEnglishFile);
                    $translations = array_merge($enTranslations, $translations);

                    $enTranslationKeys = array_keys($enTranslations);

                    foreach ($translations as $key => $translation) {
                        if (! in_array($key, $enTranslationKeys)) {
                            Arr::forget($translations, $key);
                        }
                    }
                }
            }

            ksort($translations);

            $translations = array_combine(array_map('trim', array_keys($translations)), $translations);

            $translations[$request->input('name')] = $request->input('value');

            File::put(lang_path($locale . '.json'), json_encode($translations, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        return $response
            ->setPreviousUrl(route('translations.theme-translations'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function downloadLocale(string $locale)
    {
        $file = RvMedia::getUploadPath() . '/locale-' . $locale . '.zip';

        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        if (class_exists('ZipArchive', false)) {
            $zip = new ZipArchive();
            if ($zip->open($file, ZipArchive::CREATE) !== true) {
                File::delete($file);
            }
        } else {
            $zip = new Zip($file);
        }

        $source = lang_path($locale);

        $arrSource = explode(DIRECTORY_SEPARATOR, str_replace('/' . $locale, '', $source));
        $pathLength = strlen(implode(DIRECTORY_SEPARATOR, $arrSource) . DIRECTORY_SEPARATOR);

        // Add each file in the file list to the archive
        $this->recurseZip($source, $zip, $pathLength);

        $jsonFile = lang_path($locale . '.json');

        $arrSource = explode(DIRECTORY_SEPARATOR, File::dirname($jsonFile));
        $pathLength = strlen(implode(DIRECTORY_SEPARATOR, $arrSource) . DIRECTORY_SEPARATOR);

        $this->recurseZip($jsonFile, $zip, $pathLength);

        foreach (File::directories(lang_path('vendor')) as $module) {
            foreach (File::directories($module) as $item) {
                $source = $item . '/' . $locale;

                if (File::isDirectory($source)) {
                    $arrSource = explode(
                        DIRECTORY_SEPARATOR,
                        str_replace(
                            '/vendor/' . File::basename($module) . '/' . File::basename($item) . '/' . $locale,
                            '',
                            $source
                        )
                    );
                    $pathLength = strlen(implode(DIRECTORY_SEPARATOR, $arrSource) . DIRECTORY_SEPARATOR);

                    $this->recurseZip($source, $zip, $pathLength);
                }
            }
        }

        if (class_exists('ZipArchive', false)) {
            $zip->close();
        }

        if (file_exists($file)) {
            chmod($file, 0755);
        }

        return response()->download($file)->deleteFileAfterSend();
    }

    protected function recurseZip($src, &$zip, $pathLength): void
    {
        if (File::isDirectory($src)) {
            $files = BaseHelper::scanFolder($src);
        } else {
            $files = [File::basename($src)];
            $src = File::dirname($src);
        }

        foreach ($files as $file) {
            if (File::isDirectory($src . DIRECTORY_SEPARATOR . $file)) {
                $this->recurseZip($src . DIRECTORY_SEPARATOR . $file, $zip, $pathLength);
            } else {
                if (class_exists('ZipArchive', false)) {
                    $zip->addFile($src . DIRECTORY_SEPARATOR . $file, substr($src . DIRECTORY_SEPARATOR . $file, $pathLength));
                } else {
                    /**
                     * @var Zip $zip
                     */
                    $zip->add(
                        $src . DIRECTORY_SEPARATOR . $file,
                        PCLZIP_OPT_REMOVE_PATH,
                        substr($src . DIRECTORY_SEPARATOR . $file, $pathLength)
                    );
                }
            }
        }
    }

    public function ajaxGetAvailableRemoteLocales(Manager $manager, BaseHttpResponse $response)
    {
        $remoteLocales = $manager->getRemoteAvailableLocales();

        $availableLocales = collect(Language::getAvailableLocales())->pluck('locale')->all();

        $listLanguages = Language::getListLanguages();

        $locales = [];

        foreach ($remoteLocales as $locale) {
            if (in_array($locale, $availableLocales)) {
                continue;
            }

            foreach ($listLanguages as $key => $language) {
                if (in_array($key, [$locale, str_replace('-', '_', $locale)]) ||
                    in_array($language[1], [$locale, str_replace('-', '_', $locale)])
                ) {
                    $locales[$locale] = [
                        'locale' => $locale,
                        'name' => $language[2],
                        'flag' => $language[4],
                    ];

                    break;
                }

                if (! array_key_exists($locale, $locales) &&
                    in_array($language[0], [$locale, str_replace('-', '_', $locale)])) {
                    $locales[$locale] = [
                        'locale' => $locale,
                        'name' => $language[2],
                        'flag' => $language[4],
                    ];
                }
            }
        }

        return $response
            ->setData(view('plugins/translation::partials.available-remote-locales', compact('locales'))->render());
    }

    public function ajaxDownloadRemoteLocale(string $locale, Manager $manager, BaseHttpResponse $response)
    {
        $result = $manager->downloadRemoteLocale($locale);

        return $response
            ->setError($result['error'])
            ->setMessage($result['message']);
    }
}
