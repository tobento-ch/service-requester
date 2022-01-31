<article class="error">
    
    <h1><?= $view->esc($throwable::class) ?></h1>

    <details<?= $num === 1 ? ' open' : '' ?>>
        <summary>message</summary>
        <p><?= $view->esc($throwable->getMessage()) ?></p>
    </details>

    <details<?= $num === 1 ? ' open' : '' ?>>
        <summary>code</summary>
        <div>[<?= $view->esc($throwable->getCode()) ?>] <?= $view->esc($view->toErrorType($throwable->getCode())) ?></div>
    </details>

    <details<?= $num === 1 ? ' open' : '' ?>>
        <summary>file and line</summary>
        <div><?= $view->esc($throwable->getFile()) ?> (line <?= $view->esc($throwable->getLine()) ?>)</div>
    </details>

    <details<?= $num === 1 ? ' open' : '' ?>>
        <summary>preview code</summary>
        <?php
        $lines = $view->generateCodeLines(
            file: $throwable->getFile(),
            line: $throwable->getLine(),
            numberOfLines: 10,
        );
        ?>
        <ol start="<?= $view->esc(array_key_first($lines)) ?>">
        <?php foreach($lines as $number => $line) { ?>
            <?php if ($number === $throwable->getLine()) { ?>
                <li class="highlight"><code><pre><?= $view->esc($line) ?></pre></code></li>
            <?php } else { ?>
            <li><code><pre><?= $view->esc($line) ?></pre></code></li>
            <?php } ?>
        <?php } ?>
        </ol>
    </details>

    <details>
        <summary>trace preview code</summary>
        <?php foreach($throwable->getTrace() as $trace) { ?>
            <?php
            $lines = $view->generateCodeLines(
                file: $trace['file'] ?? null,
                line: (int)($trace['line'] ?? 0),
                numberOfLines: 10,
            );
            ?>
            <div class="preview-code">
                <div><?= $view->esc($trace['file'] ?? '') ?> (line <?= $view->esc($trace['line'] ?? 0) ?>)</div>
                <ol start="<?= $view->esc(array_key_first($lines)) ?>">
                <?php foreach($lines as $number => $line) { ?>
                    <?php if ($number === $trace['line']) { ?>
                        <li class="highlight"><code><pre><?= $view->esc($line) ?></pre></code></li>
                    <?php } else { ?>
                    <li><code><pre><?= $view->esc($line) ?></pre></code></li>
                    <?php } ?>
                <?php } ?>
                </ol>
            </div>
        
        <?php } ?>
    </details>

    <details>
        <summary>trace</summary>
        <div><?= nl2br($view->esc($throwable->getTraceAsString())); ?></div>
    </details>    

</article>

<?php if ($throwable->getPrevious()) {
    $view->add(key: 'debug.throwable.'.$num, view: 'debug/throwable');
    echo $view->render('debug.throwable.'.$num, ['throwable' => $throwable->getPrevious(), 'num' => $num+1]);
} ?>