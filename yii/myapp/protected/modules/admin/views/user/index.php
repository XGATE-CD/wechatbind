<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
		<link rel="stylesheet" href="<?php echo yii::app()->request->baseURL?>/assets/admin/css/public.css" />
	<title></title>
</head>
<body>
	<?php $form=$this->beginWidget('CActiveForm',array(
        'enableClientValidation'=>true,//开启客户端验证
        'clientOptions'=>array(//客户端选项
            'validateOnSubmit'=>true,
        ),
    ))
    ?>
		<table class="table">
			<tr >
				<td class="th" colspan="2">修改密码</td>
			</tr>
			<tr>
				<td>用户名</td>
				<td><?php echo yii::app()->user->name?></td>
			</tr>
			<tr>
				<td><?php echo $form->labelEx($userModel, 'password') ?></td>
				<td>
                    <?php echo $form->passwordField($userModel,'password')?>
                    <?php echo $form->error($userModel,'password')?>
                </td>
			</tr>
            <tr>
                <td><?php echo $form->LabelEx($userModel,'password1')?></td>
                <td>
                    <?php echo $form->passwordField($userModel,'password1')?>
                    <?php echo $form->error($userModel,'password1')?>
                </td>
            </tr>
            <tr>
                <td><?php echo $form->LabelEx($userModel,'password2')?></td>
                <td>
                    <?php echo $form->passwordField($userModel,'password2')?>
                    <?php echo $form->error($userModel,'password2')?>
                </td>
            </tr>
			<tr>
				<td colspan="2">
					<input type="submit" value="修改" class="input_button"/>
					<input type="reset" class="input_button"/>
				</td>
			</tr>
		</table>
	<?php $this->endWidget()?>
</body>
</html>