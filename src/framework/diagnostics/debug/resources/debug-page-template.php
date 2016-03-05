<?php
	/**
	 * @var stdClass $v
	 * @var callable $e
	 * @var callable $slug
	 * @var nyx\framework\diagnostics\debug\delegates\displayer\FrameDescriptor $f
	 */
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $v->title ?></title>
	<style><?php echo $v->pageStyle ?></style>
</head>
<body>
<div class="container">

	<div class="stack-container">

		<div class="frames-container cf <?php echo (!$v->hasFrames ? 'empty' : '') ?>">
<?php  ?>
<?php

	foreach($v->frames as $i => $frame)
	{
		/** @var \nyx\diagnostics\debug\Frame $frame */
		$f->setFrame($frame);

?>
		<div class="frame <?php echo ($i == 0 ? 'active' : '') . ($f->isClosure() ? 'closure' : '') ?>" id="frame-line-<?php echo $i ?>">
			<div class="frame-method-info">
				<span class="frame-index"><?php echo (count($v->frames) - $i - 1) ?>.</span>
				<span class="frame-class"><?php echo $f->class ?></span>
				<span class="frame-function"><?php echo $f->function ?></span>
			</div>
	<?php if(!$f->isClosure()) { ?>
              <span class="frame-file">
                <?php echo $f->file ?>
	              <span class="frame-line"><?php echo (int) $f->line ?></span>
              </span>
	<?php } ?>
		</div>
<?php } ?>

		</div>

		<div class="details-container cf">

			<header>
				<div class="exception">
					<h3 class="exc-title">
						<?php foreach($v->name as $i => $nameSection): ?>
							<?php if($i == count($v->name) - 1): ?>
								<span class="exc-title-primary"><?php echo $e($nameSection) ?></span>
							<?php else: ?>
								<?php echo $e($nameSection) . ' \\' ?>
							<?php endif ?>
						<?php endforeach ?>
					</h3>
					<p class="exc-message">
						<?php echo $e($v->message) ?>
					</p>
				</div>
			</header>

			<div class="frame-code-container <?php echo (!$v->hasFrames ? 'empty' : '') ?>">
<?php

	foreach($v->frames as $i => $frame)
	{
		/** @var \nyx\diagnostics\debug\Frame $frame */
		$f->setFrame($frame);

		// Completely ignore closures for now since we've got no relevant data for those. An obvious @todo
		if($f->isClosure()) continue;

?>
					<?php $line = $f->line; ?>
					<div class="frame-code <?php echo ($i == 0 ) ? 'active' : '' ?>" id="frame-code-<?php echo $i ?>">
						<div class="frame-file">
							<?php $filePath = $f->file; ?>
							<?php if($filePath and $editorHref = $v->handler->getEditorHref($frame->getFile(), (int) $line)): ?>
								<a href="<?php echo $editorHref ?>" class="editor-link">
									<span class="editor-link-callout">open:</span> <strong><?php echo $f->file ?></strong>
								</a>
							<?php else: ?>
								<strong><?php echo $e($filePath ?: '<#unknown>') ?></strong>
							<?php endif ?>
						</div>
						<?php
							// Do nothing if there's no line to work off
							if($line !== null):

								// the $line is 1-indexed, we nab -1 where needed to account for this
								$range = $frame->getFileLines($line - 8, 10);
								$range = array_map(function($line){ return empty($line) ? ' ' : $line;}, $range);
								$start = key($range) + 1;
								$code  = join("\n", $range);
								?>
								<pre class="code-block prettyprint linenums:<?php echo $start ?>"><?php echo $e($code) ?></pre>
							<?php endif ?>

					</div>
<?php } ?>
			</div>

			<div class="details">
				<div class="data-table-container" id="data-tables">
					<?php foreach($v->tables as $label => $data): ?>
						<div class="data-table" id="sg-<?php echo $e($slug($label)) ?>">
							<label><?php echo $e($label) ?></label>
							<?php if(!empty($data)): ?>
								<table class="data-table">
									<thead>
									<tr>
										<td class="data-table-k">Key</td>
										<td class="data-table-v">Value</td>
									</tr>
									</thead>
									<?php foreach($data as $k => $value): ?>
										<tr>
											<td><?php echo $e($k) ?></td>
											<td><?php echo $e(print_r($value, true)) ?></td>
										</tr>
									<?php endforeach ?>
								</table>
							<?php else: ?>
								<span class="empty">empty</span>
							<?php endif ?>
						</div>
					<?php endforeach ?>
				</div>

				<?php /* List registered handlers, in order of first to last registered */ ?>
				<div class="data-table-container" id="delegates">
					<label>Delegates applicable for this exception</label>
					<?php foreach($v->handlers as $i => $handler): ?>
						<div class="delegate <?php echo ($handler === get_class($v->handler)) ? 'active' : ''?>">
							<?php echo $i ?>. <?php echo $e($handler) ?>
						</div>
					<?php endforeach ?>
				</div>

			</div> <!-- .details -->
		</div>

	</div>
</div>

<script src="//cdnjs.cloudflare.com/ajax/libs/prettify/r224/prettify.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>
	$(function() {
		prettyPrint();

		var $frameLines  = $('.frames-container .frame:not(.closure)');
		var $activeLine  = $('.frames-container .active');
		var $activeFrame = $('.active[id^="frame-code-"]').show();
		var $container   = $('.details-container');
		var headerHeight = $('header').css('height');

		var highlightCurrentLine = function() {
			// Highlight the active and neighboring lines for this frame:
			var activeLineNumber = +($activeLine.find('.frame-line').text());
			var $lines           = $activeFrame.find('.linenums li');
			var firstLine        = +($lines.first().val());

			$($lines[activeLineNumber - firstLine - 1]).addClass('current');
			$($lines[activeLineNumber - firstLine]).addClass('current active');
			$($lines[activeLineNumber - firstLine + 1]).addClass('current');
		};

		// Highlight the active for the first frame:
		highlightCurrentLine();

		$frameLines.on('click', function(e) {
			e.preventDefault();



			var $this  = $(this);
			var id     = /frame\-line\-([\d]*)/.exec($this.attr('id'))[1];
			var $codeFrame = $('#frame-code-' + id);

			if($codeFrame) {
				$activeLine.removeClass('active');
				$activeFrame.removeClass('active');

				$this.addClass('active');
				$codeFrame.addClass('active');

				$activeLine  = $this;
				$activeFrame = $codeFrame;

				highlightCurrentLine();

				$container.animate({ scrollTop: headerHeight }, "fast");
			}
		});
	});
</script>
</body>
</html>
