<?php

	$yes = 0;
	$no = 0;
	$filename = 'poll.txt';
	$vote = $_GET['vote'];
	if (!is_file($filename))
	{
		file_put_contents($filename,$yes.'<->'.$no);
	}
	else
	{
		$content = file_get_contents($filename);
		$vote_num = explode('<->',$content);
		$yes = $vote_num[0];
		$no = $vote_num[1];
	}
	$i  = ($vote == 1) ? $yes++ : $no++;
	$input = $yes.'<->'.$no;
	if (is_writable($filename))
	{
		file_put_contents($filename,$yes.'<->'.$no);
	}
?>
<h2>投票结果：</h2>
<table>
	<tr>
		<td>是:</td>
		<td>
			<img src="poll.gif" width="<?php echo 100*round($yes/($yes+$no),2);?>" height="20" alt="" />
			<?php echo 100*round($yes/($yes+$no),2);?>%
		</td>
	</tr>
	<tr>
		<td>否:</td>
		<td>
			<img src="poll.gif" width="<?php echo 100*round($no/($yes+$no),2);?>" height="20" alt="" />
			<?php echo 100*round($no/($yes+$no),2);?>%
		</td>
	</tr>
</table>