<?php
require_once "vendor/autoload.php";
use FastSimpleHTMLDom\Document;

if ($_GET['city'] === 'pt') {
    echo parseCity('pt');
} elseif ($_GET['city'] === 'al') {
    echo parseCity('al');
}

function parseCity($city) {
    // Find all post blocks
    $result = [];
    $post = [];
    $counter = Document::file_get_html(getCity($city));
    $counter = $counter->find('div.modern-page-navigation')->find('a', 5)->plaintext;

    for ($i = 1; $i <= $counter; $i++) {
        // Create DOM from URL
        $html = Document::file_get_html(getCity($city) . '&PAGEN_1=' . $i);

        foreach($html->find('div.inner') as $key => $item) {
            if (str_contains($item->find('div.aa_std_name')->plaintext, 'Чехол')) {
                if (str_replace('-', '', mb_substr($item->find('div.aa_st_img')->plaintext, 0, 3)) < 90) {
                    continue;
                }
            }

            $post[] = [
                'discount' => str_replace('-', '', mb_substr($item->find('div.aa_st_img')->plaintext, 0, 3)),
                'title'    => $item->find('div.aa_std_name')->plaintext,
                'price'    => $item->find('div.aa_st_buyblock')->find('div', 0)->find('span', 0)->plaintext,
                'link'     => $item->find('div.aa_std_name')->find('a', 0)->href,
            ];
        }

        $result = array_filter($post, function($a) use($i) {
            return $a['discount'] > 50;
        });
    }

    return json_encode($result, JSON_PRETTY_PRINT);
}

function getCity($city) {
    return sprintf('https://www.mechta.kz/catalog-of-goods/discounted-products/?setcity=%s&sort=created_date&adesc=desc', $city);
}