<?php
/**
 * ELipsum class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @link http://code.google.com/p/yiiext/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
/**
 * ELipsum widget
 *
 * It generate simply dummy text.
 *
 * Usage as widget:
 *
 * <pre>
 * $this->widget('ext.yiiext.widgets.ELipsum',array(
 *     'paragraphs'=>2,
 *     'words'=>50,
 * ));
 * </pre>
 *
 * Usage static methods:
 *
 * <pre>
 * Yii::import('ext.yiiext.widgets.lipsum.ELipsum');
 * echo ELipsum::paragraphs(2);
 * echo ELipsum::words(100);
 * </pre>
 *
 * Usage in models:
 *
 * <pre>
 * public function rules()
 * {
 *     Yii::import('ext.yiiext.widgets.lipsum.ELipsum');
 *     return array(
 *         array('content','default','value'=>ELipsum::paragraphs(),'on'=>'insert'),
 *     );
 * }
 * </pre>
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext.widgets.lipsum
 */
class ELipsum extends CWidget
{
	/**
	 * @var integer the number of paragraphs. Set 0 (zero) to get random from 1 to 10 paragraphs.
	 * Defaults to 0.
	 */
	public $paragraphs=0;
	/**
	 * @var integer the number of words per paragraph. Set 0 (zero) to get random from 5 to 100 words.
	 * Defaults to 0.
	 */
	public $words=0;
	/**
	 * @var boolean which start with "Lorem ipsum dolor sit amet".
	 * Defaults to true.
	 */
	public $loremIpsumFirst=true;
	/**
	 * @var string the paragraph's wrapper tag.
	 * Defaults to 'p'.
	 */
	public $paragraphTag='p';

	/**
	 * Run widget
	 */
	public function run()
	{
		echo self::paragraphs($this->paragraphs,$this->words,$this->paragraphTag);
	}
	/**
	 * Generate paragraphs.
	 *
	 * @param integer $count the number of paragraphs.
	 * @param integer $words the number of words per paragraph.
	 * @param boolean $loremIpsumFirst which start with "Lorem ipsum dolor sit amet".
	 * @param string $wrapperTag the paragraph's wrapper tag.
	 * @return string
	*/
	public static function paragraphs($count=0,$words=0,$loremIpsumFirst=true,$wrapperTag='p')
	{
		$text='';
		$count=empty($count)?rand(1,10):$count;
		for($i=0;$i<$count;$i++)
		{
			if(!empty($wrapperTag))
			{
				$text.=CHtml::tag($wrapperTag,array(),self::words($words,$loremIpsumFirst&&$i==0));
			}
		}
		return $text;
	}
	/**
	 * Generate words.
	 *
	 * @param mixed $count the number of words.
	 * @param mixed $loremIpsumFirst which start with "Lorem ipsum dolor sit amet".
	 * @return string
	*/
	public static function words($count=0,$loremIpsumFirst=true)
	{
		$library=array(
			"lorem",'ipsum',"dolor","sit","amet","integer","vut","nunc","risus","a",
			"sagittis","turpis","nunc","eu","urna","urna","pellentesque","porttitor","est","ut",
			"augue","cursus","scelerisque","in","hac","habitasse","platea","dictumst","sed","ut",
			"odio","a","ultricies","dapibus","cum","sociis","natoque","penatibus","et","magnis",
			"dis","parturient","montes","nascetur","ridiculus","mus","etiam","vel","lacus","magna",
			"nec","aliquam","augue","lundium","integer","porttitor","porta","in","rhoncus","adipiscing",
			"diam","ultrices","turpis","auctor","aenean","pulvinar","egestas","ac","placerat","sed",
			"lectus","mauris","rhoncus","mid","tincidunt","dignissim","elementum","in","odio","duis",
			"vel","magna","elit","phasellus","tincidunt","nisi","pid","pulvinar","placerat","purus",
			"augue","aliquet","tortor","et","tristique","turpis","enim","nec","nisi","proin",
			"facilisis","adipiscing","enim","ac","mattis","arcu","elementum","et","cras","massa",
			"non","velit","tempor","scelerisque","ac","quis","eros",
		);
		$punctuations=array(
			'.',',','!',
		);

		$text='';
		$libraryCount=count($library);
		$punctuationCount=count($punctuations);
		$count=empty($count)?rand(5,100):$count;
		for($i=0,$p=0,$begin=true;$i<$count;$i++)
		{
			if($loremIpsumFirst&&$i<5)
			{
				$word=$library[$i];
			}
			else
			{
				$word=$library[rand(1,$libraryCount)-1];
			}
			$text.=$begin?ucfirst($word):$word;
			$punctuation=($i-$p+1)%rand(3,10)==0&&$i+1!=$count?$punctuations[rand(1,$punctuationCount)-1]:false;
			if($punctuation!==false)
			{
				$text.=$punctuation;
				$p=$i;
			}
			$begin=$punctuation!==false&&$punctuation!=',';
			$text.=$i+1!=$count?' ':'.';
		}
		return $text;
	}
}
