<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="icon" href="<?php echo base_url("favicon.ico") ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="theme-color" content="#000000" />
    <meta name="description" content="" />

    <link rel="manifest" href="<?php echo base_url("manifest.json") ?>" />

    <title></title>

</head>

<body>
    <noscript>You need to enable JavaScript to view this page.</noscript>
    <div id="root"></div>

</body>

<script>
let manifest = <?php echo file_get_contents(FCPATH . "manifest.json") ?>;

let assets = manifest.entrypoints.main.assets;

assets.js.forEach(asset => {
    let script = document.createElement('script');
    script.type = "text/javascript";
    script.src = asset;
    document.querySelector('body').appendChild(script);
});

assets.css.forEach(asset => {
    let style = document.createElement('link');
    style.href = asset;
    style.rel = "stylesheet";
    style.type = "text/css";
    style.media = "all";
    document.querySelector('body').appendChild(style);
});

document.querySelector("title").textContent = manifest.short_name;
document.querySelector("meta[name='description']").content = manifest.name;
</script>

</html>