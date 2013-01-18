[OPTIONS]
Auto Index=Yes
Binary TOC=Yes
Binary Index=Yes
Compatibility=1.1 or later
Compiled File=<?php echo $book->name; ?>.chm
Contents File=<?php echo $book->name; ?>.hhc
Default Window=main
Default Topic=index.html
Display compile progress=Yes
Error log file=_errorlog.txt
Full-text search=Yes
language=0x804 chinese (prc)
Title=<?php echo $book->subject; ?> for QeePHP <?php echo Q::version(); ?>


[WINDOWS]
main="<?php echo $book->subject; ?> for QeePHP <?php echo Q::version(); ?> - 生成时间: <?php echo date('Y-m-d H:i', CURRENT_TIMESTAMP); ?>","<?php echo $book->name; ?>.hhc",,"index.html","index.html",,,,,0x63520,250,0x104e,[10,10,900,700],0xb0000,,,,,,0

