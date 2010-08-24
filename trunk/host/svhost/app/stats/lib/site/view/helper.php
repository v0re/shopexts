<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 

 * ����Ϊÿ��app�ӹ�ǰ̨ҳ�����ʾ����
 * һ�����������function_header��function_footer���ڽӹ�ͷ��
 */
class stats_site_view_helper 
{
    /**
     * �ӹ�ͷ���ķ���
     * @param array ͷ���ӹ�ʱ���ݹ����Ĳ���
     * @param object ҳ���render
     */
    public function function_header($params, &$smarty)
    {
        return '';
    }
    
    /**
     * �ӹܵײ��ķ���
     * @param array �ӹܵײ�ʱ���ݹ����Ĳ���
     * @param object ҳ���render
     */
    public function function_footer($params, &$smarty)
    {
        if ($smarty->is_splash === false)
        {
            $obj_stats_modifier = kernel::single('stats_modifiers');
            return $obj_stats_modifier->print_footer();
        }
        else
        {
            return '';
        }
    }
}