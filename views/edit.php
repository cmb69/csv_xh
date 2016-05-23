<form action="<?php echo $this->controller->url('create')?>" method="POST">
<?php foreach ($columns as $column):?>
    <p>
        <label for="csv_id<?php echo ++self::$id?>"><?php echo $column?></label>
        <input id="csv_id<?php echo self::$id?>" type="text"
                name="csv_<?php echo $column?>" value="<?php echo $record[$column]?>"/>
    </p>
<?php endforeach?>
    <p>
        <button>SAVE</button>
        <button type="reset">RESET</button>
    </p>
</form>
