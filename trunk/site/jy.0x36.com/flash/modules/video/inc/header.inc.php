<?
/***************************************************************************
*
* IMPORTANT: This is a commercial product made by BoonEx Ltd. and cannot be modified for other than personal usage.
* This product cannot be redistributed for free or a fee without written permission from BoonEx Ltd.
* This notice may not be removed from the source code.
*
***************************************************************************/

if(empty($GLOBALS['sModule'])) $GLOBALS['sModule'] = "video";
$GLOBALS['sModuleUrl'] = $GLOBALS['sModulesUrl'] . $GLOBALS['sModule'] . "/";
$GLOBALS['sFilesDir'] = "files/";
$GLOBALS['sFilesUrl'] = $GLOBALS['sModuleUrl'] . $GLOBALS['sFilesDir'];
$GLOBALS['sFilesPath'] = $GLOBALS['sModulesPath'] . $GLOBALS['sModule'] . "/" . $GLOBALS['sFilesDir'];
$GLOBALS['sServerApp'] = "video";
$GLOBALS['sStreamsFolder'] = "streams/";
$GLOBALS['aConvertTmpls'] = array(
	"playX264" => $GLOBALS['sFfmpegPath'] . " -y -i #input# -r 30000/1001 -b #bitrate#kb -bt 128kb -vcodec libx264 -deblockalpha 0 -deblockbeta 0 -flags +loop+mv4 -cmp 256 -partitions +parti4x4+parti8x8+partp4x4+partp8x8+partb8x8 -me_method hex -me_range 16 -subq 7 -bf 0 -b_strategy 2 -bframebias 0 -trellis 1 -flags2 +mixed_refs -coder 1 -refs 8 -g 300 -keyint_min 25 -sc_threshold 40 -rc_eq 'blurCplx^(1-qComp)' -qcomp 0.0 -complexityblur 20.0 -qblur 1.0 -qmin 10 -qmax 51 -qdiff 4 -i_qfactor 0.71 -b_qfactor 0.77 -s #size# #audio_options# #output#",
	"play" => $GLOBALS['sFfmpegPath'] . " -y -i #input# -r 25 -b #bitrate#kb -sameq -s #size# #audio_options# #output#",
	"image" => $GLOBALS['sFfmpegPath'] . " -y -i #input# #size# -ss #second# -vframes 1 -an -sameq -f image2 #output#",
	"mobile" => $GLOBALS['sFfmpegPath'] . " -y -i #input# -r 25 -s #size# -b 512kb -sameq -ab 128kb -acodec libfaac -ac 1 -ar 44100 #output#"
);
?>
