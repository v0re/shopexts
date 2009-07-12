<?php

	class Gimage
	{
		public $srcImageWidth;			//原始图片宽度
		public $srcImageHeight;			//原始图片高度度
		public $srcImageExt;			//原始图片文件扩展名
		public $srcImageMime;			//原始图片mime类型
		public $destImageDir;			//生成目标图片的存放目录
		public $srcImageMovedDir;		//原始图片转移存放目录
		public $functionMime;			//通过扩展名动态创建函数

		/*public function __construct($srcImageFile)
		{
			$this->getImageInfo($srcImageFile);
			$this->setDestImageDir($srcImageFile);
		}*/

		//获取图片文件信息
		public function getImageInfo($srcImageFile)
		{
			if (is_file($srcImageFile))
			{
				$imageInfo = getimagesize($srcImageFile);
				$this->srcImageWidth = $imageInfo[0];
				$this->srcImageHeight = $imageInfo[1];
				$this->srcImageMime = $imageInfo['mime'];
				$functionMime = explode('/',$imageInfo['mime']);
				$this->functionMime = $functionMime[1];
				//原始图片文件扩展名
				switch ($imageInfo[2])
				{
					case 1 :
						$this->srcImageExt='gif';
						break;
					case 2 :
						$this->srcImageExt='jpg';
						break;
					case 3 :
						$this->srcImageExt='png';
						break;
					case 4 :
						$this->srcImageExt='swf';
						break;
					case 5 :
						$this->srcImageExt='psd';
						break;
					case 6 :
						$this->srcImageExt='bmp';
						break;
					case 7 :
					case 8 :
						$this->srcImageExt='tiff';
						break;
					default:
						$this->srcImageExt = substr($srcImageFile,stripos($srcImageFile,'.'));
				}

			}
		}

		//设置生成目标图片的存放目录
		public function setDestImageDir()
		{
			$destFileNameDir = 'images/goods/'.date('Ymd').'/';
			if (!is_dir($destFileNameDir))
			{
				mkdir($destFileNameDir,0777,true);
			}
			$this->destImageDir = $destFileNameDir;
		}

		//获取生成目标图片的名称
		public function targetFileName($srcImageFile)
		{
			$getMD5Str = substr(md5($srcImageFile),rand(0,16),16);
			$targetFileName = $getMD5Str.'.'.$this->srcImageExt;
			return $targetFileName;
		}

		//拷贝原始图片
		public function copySrcImage($srcImageFile)
		{
			//原始图片移动到的目录
			$srcImageMovedDir = 'home/uplod/gpic/'.date('Ymd').'/';
			//目录是否存在,若不存在则创建目录
			if (!is_dir($srcImageMovedDir))
			{
				mkdir($srcImageMovedDir,0777,true);
			}
			//原始图片存放的完整路径,包括文件名
			$destImageFile = $srcImageMovedDir.basename($srcImageFile);
			if (is_file($srcImageFile))
			{
				//拷贝原始图片
				copy($srcImageFile,$destImageFile);
				//改变文件权限
				chmod($destImageFile,0644);
			}
			//返回原始图片移动后的路径
			return $destImageFile;
		}

		//重采样原始图片进行缩,然后保存到指定目录
		public function resampledImage($srcImageFile,$size)
		{
			if (is_file($srcImageFile))
			{
				//初始化,得到图片信息
				$this->getImageInfo($srcImageFile);
				$this->setDestImageDir();
				//根据给定的尺寸，生成不同的图片
				foreach ($size as $key => $var)
				{
					$width = $key;
					$height = $var;
					if ($width && ($this->srcImageWidth < $this->srcImageHeight))
					{
						$width = ($height / $this->srcImageHeight) * $this->srcImageWidth;
					}
					else
					{
						$height = ($width / $this->srcImageWidth) * $this->srcImageHeight;
					}
					$targetFileName = $this->targetFileName($srcImageFile);
					//动态调用,创建原图片资源
					$call = 'imagecreatefrom'.$this->functionMime;
					$srcImgRes =$call($srcImageFile);
					//创建目标图片资源
					$destImgRes = imagecreatetruecolor($width,$height);
					//重新采样原图片
					imagecopyresampled($destImgRes,$srcImgRes,0,0,0,0,$width,$height,$this->srcImageWidth,$this->srcImageHeight);
					//动态调用,生成目标图片文件
					$dynamicCall = 'image'.$this->functionMime;
					if ($dynamicCall($destImgRes,$this->destImageDir.$targetFileName))
					{
						echo '生成图片<span style="color:red">'.$this->destImageDir.$targetFileName.'</span>成功!<br />';
					}
					//改权限
					chmod($this->destImageDir.$targetFileName,0644);
				}
				//拷贝原图片
				$targetImgPath = $this->copySrcImage($srcImageFile);
			}
		}
	}
?>