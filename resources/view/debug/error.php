<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Error</title>
    <meta name="description" content="">
    <meta name="author" content="">

    <style type="text/css">
    <!--
    /* CSS Document */
    * { margin: 0; padding: 0;}
    html { box-sizing: border-box;}
    *, *:before, *:after { box-sizing: inherit;}
    body { color: #0d0e0e; font-size: 100%; margin: 30px;}
        
    h1{ margin: 0; font-size: 2em;}
    h2{ margin: 0; font-size: 1.375em;}
    
    details{ margin-top: 15px; font-size: 1.125rem;}
    summary{ font-size: 1rem; font-weight: 600; cursor: pointer; margin-bottom: 5px;}
    
    .error{ margin-bottom: 30px; padding: 10px; border: 10px solid pink;}
    .highlight{ background: lightyellow;}
    .preview-code{ margin-bottom: 30px;}
    .preview-code div{ margin-bottom: 5px;}
    
    ol{ list-style-position: inside; background: #f9f9f9; overflow-y: auto;}
    pre{ display: inline; width: 100%;}
    -->
    </style>

</head>

<body>
    <?= $view->render('debug/throwable', ['throwable' => $throwable, 'num' => 1]) ?>
</body>
</html>