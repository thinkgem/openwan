<?php

$sections = array();
foreach ($chapter->sections as $section)
{
    $sections[$section->group_name][] = $section;
}
$book = $chapter->book;

?>

<ul class="grouped-sections">

<?php foreach ($sections as $group_name => $grouped_sections): ?>

    <li><?php echo h($group_name); ?>:

<?php
$links = array();
foreach ($grouped_sections as $section)
{
    if ($section->has_contents)
    {
        $links[] = '<a href="' . $book->url($section) . '">' . h($section->subject) . '</a>';
    }
    else
    {
        $links[] = '<s>' . h($section->subject) . '</s>';
    }
}

echo implode("\n", $links);
?>

    </li>

<?php endforeach; ?>

</ul>

