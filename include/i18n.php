<?php
/**
 * Lightweight i18n helper for the Notice Board module.
 *
 * Zabbix's own _() only translates strings already present in Zabbix's
 * core gettext catalog, so module-specific strings (e.g. "Notice Board",
 * "No notices registered yet.") are returned unchanged by _() no matter
 * which language the user has selected. nb__() reads this module's own
 * locale/<lang>/LC_MESSAGES/module.po catalog instead, keyed off the
 * Zabbix UI language chosen by the logged-in user.
 */

function nb_lang(): string {
    $lang = '';
    if (class_exists('CWebUser') && isset(\CWebUser::$data['lang'])) {
        $lang = (string) \CWebUser::$data['lang'];
    }

    return (strncmp($lang, 'pt', 2) === 0) ? 'pt_BR' : 'en_US';
}

function nb_catalog(string $lang): array {
    static $cache = [];
    if (isset($cache[$lang])) {
        return $cache[$lang];
    }

    $catalog = [];
    $file    = __DIR__ . '/../locale/' . $lang . '/LC_MESSAGES/module.po';

    if (is_file($file)) {
        $content = file_get_contents($file);
        preg_match_all(
            '/msgid\s+"((?:[^"\\\\]|\\\\.)*)"\s*\n\s*msgstr\s+"((?:[^"\\\\]|\\\\.)*)"/',
            $content,
            $matches,
            PREG_SET_ORDER
        );
        foreach ($matches as $match) {
            $msgid  = stripcslashes($match[1]);
            $msgstr = stripcslashes($match[2]);
            if ($msgid !== '' && $msgstr !== '') {
                $catalog[$msgid] = $msgstr;
            }
        }
    }

    return $cache[$lang] = $catalog;
}

function nb__(string $msgid): string {
    if ($msgid === '') {
        return '';
    }

    $lang = nb_lang();
    if ($lang === 'en_US') {
        return $msgid;
    }

    $catalog = nb_catalog($lang);
    return $catalog[$msgid] ?? $msgid;
}
