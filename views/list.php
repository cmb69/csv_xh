<table>
    <thead>
<?php foreach ($columns as $column):?>
        <th><?php echo $column?></th>
<?php endforeach?>
    </thead>
    <tbody>
<?php foreach ($records as $record):?>
        <tr>
<?php foreach ($record as $field):?>
            <td><?php echo $field?></td>
<?php endforeach?>
        </tr>
<?php endforeach?>
    </tbody>
</table>
<p><a href="<?php echo $this->controller->url('create')?>">NEW RECORD</a></p>
