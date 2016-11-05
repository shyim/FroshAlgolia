{extends file="parent:frontend/index/header.tpl"}

{block name="frontend_index_header_javascript_modernizr_lib" append}
    <!-- Add Algolia js search-only (lite) library -->
    <script src="https://cdn.jsdelivr.net/algoliasearch/3/algoliasearchLite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/instantsearch.js/1/instantsearch.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/instantsearch.js/1/instantsearch.min.css">
    <script src="https://cdn.jsdelivr.net/autocomplete.js/0/autocomplete.min.js"></script>
{/block}
