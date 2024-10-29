<?php
/**
 * Plugin Name: Dropshipping on Alibaba.com
 * Description: Dropship products on sale from global manufacturers, no MOQ.
 * Version: 1.0.3
 * Author: Alibaba
 * Author URI: https://www.alibaba.com
 * Text Domain: Alibaba
 *
 * @package Alibaba
 */

if (!defined('WPINC'))
{
    die;
}

define("ALIBABA_PLUGIN_NAME", "Alibaba");
define("ALIBABA_PLUGIN_PAGE", "alibaba");

add_action('admin_init', array('Alibaba_Product', 'check_woocommerce'));
add_action('admin_menu', array('Alibaba_Product', 'plugin_menu'));


class Alibaba_Product
{
    /* Plugin Menu */
    public function plugin_menu()
    {
        $image		= Alibaba_Product::alibaba_logo();
        add_menu_page(
            esc_html__(ALIBABA_PLUGIN_NAME, ALIBABA_PLUGIN_PAGE),
            esc_html__(ALIBABA_PLUGIN_NAME, ALIBABA_PLUGIN_PAGE),
            'manage_woocommerce',
            ALIBABA_PLUGIN_PAGE,
            array('Alibaba_Product', 'options_page_html'),
            $image,3
        );
    }
    /* Check if WooCommerce exists */
    public function check_woocommerce()
    {
        if(is_plugin_active('woocommerce/woocommerce.php'))
        {
            return true;
        }
        $plugin_name	= plugin_basename(ALIBABA_PLUGIN_NAME);
        if(current_user_can('activate_plugins') && is_plugin_active($plugin_name))
        {
            deactivate_plugins(plugin_basename($plugin_name));
            if(isset($_GET['activate']))
            {
                unset($_GET['activate']);
            }

        }
        add_action('admin_head',
            function() {
                echo '<div class="error"><p>'.ALIBABA_PLUGIN_NAME.'</p>';
                echo '<p><b>Error: </b>WooCoommerce needs to be installed and activated before you can activate '.ALIBABA_PLUGIN_NAME.' plugin.</p></div>';
            }
        );

    }

    public function options_page_html()
    {
        echo '<style>.ali-orange{border:0; padding:10px 40px; background-color:#ff6600; color:#ffffff; text-decoration:none; -moz-border-radius:20px; -webkit-border-radius:20px; border-radius:20px; font-weight: bold;}.ali-orange:hover{color:#eeeeee}</style>';

        $AuthInfo	= array();
        $AuthInfo['shop_url']	= get_site_url();
        $tmp	= parse_url($AuthInfo['shop_url']);
        $AuthInfo['shop_name']	= $tmp['host'];
        $AuthInfo['admin_url']	= get_admin_url(null, 'admin.php?page='.ALIBABA_PLUGIN_PAGE);
        $AuthInfo['connected']	= Alibaba_Product::get_connect_status($AuthInfo['shop_name']);
        if($AuthInfo['connected'] == "true")
        {
            $nonce	= wp_create_nonce(ALIBABA_PLUGIN_PAGE.'-disconnect-nonce');
            $image	= Alibaba_Product::connected_image();
            echo '<center>';
            echo '<p><h2 style="font-size:2em">Connected successfully!</h2></p>';
            echo '<p><br/></p>';
            echo '<p></p>';
            echo '<p><img width="450" height="100" src="'.$image.'"/></p>';
            echo '<p>Your store has been successfully connected to your Alibaba.com account.</p>';
            echo '<br/>';
            echo '<p><a class="ali-orange" target="_blank" href="https://dropshipping.alibaba.com/saas/search-product.html?channel=woocommerce" onclick="jQuery(\'#plugin_go_alibaba\').attr(\'src\', \'https://webhook.alibaba.com/trace?shop='.$AuthInfo['shop_name'].'&channel=woocommerce&action=goto\')">Go to Alibaba.com Dropshipping</a>';
            echo '</center>';
            echo '<img id="plugin_go_alibaba" width="1" height="1" src="https://webhook.alibaba.com/trace?shop='.$AuthInfo['shop_name'].'&channel=woocommerce&action=connected"/>';
            echo '<br/><br/><br/><br/><br/><br/><br/><br/><br/>';
        }
        else
        {
            $image	= Alibaba_Product::connecting_image();
            echo '<center>';
            echo '<p><h2 style="font-size:2em">Connect to Alibaba.com</h2></p>';
            echo '<p><br/></p>';
            echo '<p></p>';
            echo '<p><img width="450" height="120" src="'.$image.'"/></p>';
            echo "<p>You're almost done! Just 2 more steps to have your WooCommerce store connected</p>";
            echo "<p>to Alibaba.com for automatic order fufillment.</p>";
            echo '<br/>';
            echo '<p><a class="ali-orange" target="_blank" href="https://login.alibaba.com/?flag=1&return_url='.rawurlencode("https://webhook.alibaba.com/install/woocommerce?callback=".$AuthInfo['admin_url'].'&shop='.$AuthInfo['shop_name']).'" onclick="jQuery(\'#plugin_go_auth\').attr(\'src\', \'https://webhook.alibaba.com/trace?shop='.$AuthInfo['shop_name'].'&channel=woocommerce&action=auth\')">Connect Now</a>';
            echo '</center>';
            echo '<br/><br/><br/><br/><br/><br/><br/><br/><br/>';
            echo '<img id="plugin_go_auth" width="1" height="1" src="https://webhook.alibaba.com/trace?shop='.$AuthInfo['shop_name'].'&channel=woocommerce&action=view"/>';
        }
    }
    /* Check if connect alibaba */
    public function get_connect_status($shop_name)
    {
        $params		= array();
        $params['method']		= "GET";
        $params['httpversion']	= "1.1";
        $params['headers']		= array('Content-Type' => 'application/json');
        $response	= wp_remote_request("https://webhook.alibaba.com/check/woocommerce?shop=".$shop_name);
        return trim($response['body']);
    }
    /* Image for alibaba icon */
    function alibaba_logo()
    {

        $image[]	= "iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAACXBIWXMAAAsSAAALEgHS3X78AAACr0lEQVR4nK3UbyzUARzH8d+JwkobKuZI5AFb/xBZHCFRYbHRcqEu1ejcVSRaTjSELnf+3EpHMr";
        $image[]	= "L+qe13bbVEW6uNJ9b0zzyz/jzoqgf1oCe9e3B37pK1tXnwefraPt99v18BEBYiAiAMjnYKUWu9vYard6i5sfcufdkmejJNdO00YUgx0ZZo4lKciZYtJhoiTZzfeH+4MFCTEOjuPzjaaYeiQry9PnVl";
        $image[]	= "jzCQC9ezoDsDrqRB53bQJ4A2FpqjoSEC6taDJgzOhEBl0Gu1zCd4FhquSVExkGNF0ucgW6EpGuptSKgFqQiEk1LMJb6GWYiB3Nv07gFjOlxOhY5k0MdbkSioD4fadVAdClU2xA9Uq6DEa9IO9WaJGH";
        $image[]	= "dbkSTQxcNFK6JLhq5c6M6H1jSoDIYTflC6Eko84YjHtB0ypot/Idf2w8Q9+PYBvszAIy2M34IHF0DlA8WecHgZKNwcoMupIu2JoJOBPgnG+mFmAh63QkcmaNZBxRqoCYexW1DqA4eXwkFXKHB2gNqT";
        $image[]	= "RFploNsG70bg5w+4ug+aZaBNgfIASx2xHvqUdiR/EcgFB0gnE2mJgedG4BeMGsCQDY2xUBkCx33BqIBnvVDsDQeWWBEnkEscoJYYEUMGfP8M44NQFwGng6DMH9S+0JQMb5+CWgpFHqD0g9o4UPrPgR";
        $image[]	= "ojRdrS4Ot7GKoC/S7QpsK5SFCugCcGmH4BD/Vwpwaad0G+M+QJkOdYrW6DiCbMAtwshx4FNMhAuRKOLoeyYKiNBdVqSx0LMA+kCRty3FZUPnDMy4IccofCxSCXzAVseWM/Ebn0LKdsyCrbooHCDQpc";
        $image[]	= "/oVgzhH6Z6GEAFcpZdJJSv8PIU/4qA4Vwv94I+rNAUHmIs92jni8ROE2RYHLFHLJFHnCfHllzhH61KHCJhcnQWKf0QLkN1XmXl6Yt85nAAAAAElFTkSuQmCC";
        return "data:image/png;base64,".implode("", $image);
    }
    /* Image For Connect successfuly */
    function connected_image()
    {
        $image		= array();
        $image[]	= "iVBORw0KGgoAAAANSUhEUgAAAcIAAABkCAYAAAAVKjACAAAACXBIWXMAAAsSAAALEgHS3X78AAAdlklEQVR4nO2dB3Rc1ZnHOScnZ7NZtgE5y24SkpAekrCwkAQSSEIgCQmBhGIIgdCLccUN2xj33n";
        $image[]	= "DDxgbLyBVbkmW5yFazJFvNVi+2JEuyZHVbU9S79O387/BG02f0NDN35uX7n/M7st68d999943vX9+t17W2tl3HMAzDMP+oSM8AwzAMw8hEegYYhmEYRibSM8AwDMMwMpGeAYZhGIaRifQMMAzDMIxM";
        $image[]	= "pGeAYRiGYWQiPQMMwzAMIxPpGWAYhmEYmUjPAMMwDMPIRHoGGIZhGEYm0jPAMAzDMDKRngGGYRiGkYn0DDAMwzCMTHyWUFTUYbr55pvpuuuuY7wAZYUyk/0FYBgmOOA6VF4d6rMX+PnPf57Cw8Opra";
        $image[]	= "2NWO6FMkJZoczYDBmG4Tp0dPJ1HeqTlwhnRqZYoxPKDGUn+z8hwzBy4TpUnXxVh/rkJSJMdfZXTHNzMz3xxBMC/BvKz8+nBx98kMaPH08DAwPiWHR0NN1///20Zs2agBaibKHMUHay/xMyDCMXrkPV";
        $image[]	= "yVd1qM9eojPNmTPH0p6Lf0MPPfSQ5VhkZKQ4dsMNN1iOVVdXB6oMg0JshAzDcB2qXkFvhAhblZejhP2TJ08Wv3/uc5+jgoICcezee+8Vx2666SZqb28PWAEGg9gIGYbhOlS9gt4IoZSUFIGiwcFBio";
        $image[]	= "mJoaKiIssxhLcRERFUV1fn1wILRrERMgzDdah6hYQRstyLjfAfg8pr9bS7PJFmZofRX1KX0+2xU+iWo6/SDYefoy9GjhM/8TuO43Och/Nxney8M/6H61D1Cioj3Lt3L6MCNkLtUthUSe/l7aU7Tk6l";
        $image[]	= "f4p4UjW4HukgPdnPxPgHrkPl1qE+e4kTJ05kVMBGqD1iqjLpN4nzxmR+rngw6T1C+rKfkfEtXIfKrUN99hJZ6sRGqB0SanLpnriZfjFAe3Af3E/2MzO+getQ9WIj1IDYCEOf6pYGeil9Q0AM0B7cF/";
        $image[]	= "eXXQbM2OA6VL3YCDUgNsLQBlHZN46+JsUEFXB/jg5DG65D1YuNUANiIwxd1hUdFiM+ZZqgAvKB/MguE0YdXIeqFxuhBsRGGJrMytkl3fycgXzJLhtm9HAdql5shBoQG2HoMfHch9INzx3In+wyYkYH";
        $image[]	= "16HqxUaoAbERhhYL8/ZJNzpvQD5llxXjPVyHqhcboQbERhg6YKWXQJrZ/8VPo5kFn9DmS8dpR1UcLblwkP50din9W9Rfvboe+ZVdZox3cB2qXmyEGhAbYWhQ0FhBNx5+LiAG+OjZZVRorHb5nWnt76";
        $image[]	= "T5xfvpPw4/6zYd5Bf5ll12jGe4DlUvNkINiI0w+DEYjfTTuBl+N0CM/NxSccLr706RsYa+c2K82zSRb+Rfdhky7uE6VL3YCDUgNsLgZ31xtN9N8F8in6aY+nOj/v7UdrXQV46+7Dbt9035l12GjHu4";
        $image[]	= "DlUvNkINiI0wuKm6Vk9fin4+KE1QUfLVYrfpI/9VvItFUMN1qHqxEWpAbITBDbZDkmWCg8ODtLcmhZ7NXEePnV0uBstc6211eu5f0la4vQ+eQ3ZZMq7hOlS92Ag1IDbC4OVKS5NfB8i4M8HOgR56OH";
        $image[]	= "WRwzVfPfoKFbfWOJx/qinP7b3wHHge2WXKOIfrUPViI9SA2AiDl5WFEdJM8IFk19s4/W/cVBoaHrK5pnewn/49yv0oUjyP7DJlnBNsdehwfy/1l6VT17F11L71ZTLMv4/0b99Guje+QroJt5Jh9t3U";
        $image[]	= "tuGv1H1yCw21XpWaVzZCDYiNMHi58+TbQWeCCtn6Codrbzs5ye01eB7ZZco4Jxjq0OGhQeoriKP27W+QbtK3SPf6/9hiMsG2Tc+RfvqPbI+/9XXqTtguLd+aMsL+vkG6WttODZXGkOBaXTsN9A8GxU";
        $image[]	= "tkfE9W/UW/mOA/R4yjI/VZTr8L3pogcGakv0ia7fE6PJfssmUckWmEQ12t1HV8PRlm3eloftaM/xr1pB0wGeYQ9Veep459s0k38VbL5z0Zh6TkXxNG2Nc7SLE7i2np32JpwZPHQgrk+eSuEurvVW+I";
        $image[]	= "bITByfKCQ34xQqwS40yjMUHgrJ/w+7ETPF6H55JdtowjMoxwuK+bumLWkH7yd1yb38RvUuu6J6kzain15hylweZKmzSGjE1kXPawONe44FcBfwYo5I0QEdXHc9OkG9pY2TkvzfQsQ54f2E8vkfE9f0";
        $image[]	= "h2HKgyVmB0zjRaE3SWTsdAN10f+YzHa/FcssuWcSTQRthXcIoMs+9y2vzZuuYv1BW7ifov59Hw4IDHtPorsz+LGG8JQM4dFfJGeOZwhc9NacOEJNq9JIsWP3M8oGZ4NtqxzyZQL5HxPf4YLZrQXODw";
        $image[]	= "/kdrgt8+8SbVdekc0omuy/LqejyX7LJlHAmUESIK7Ngzy66P7xvUvu0V6s2MpKFOw6jT7CtKFOnop/3QDzn2rJA3ws1Tki1GcmDVeYralEdLnj2h2ow+mJZi6beru2QIqBFumZos7SUyvqWsucbnJo";
        $image[]	= "hpD8PDww7v/6n01aMywepO5yP0fn3aezPF88kuYy1ibKojw+m9ZIheQ4bkfYTfvb02EEaI0Z3Gpb+zMUHR59fTMaZ0295/WqTVETbZRzkdnULeCBc9bY7akg+VW45lxV5WbUYXMhtt0l/3RkLAjBAR";
        $image[]	= "aKBeYnO9jjJiL1H8vmLKPHWJrjbquWLzIccuexddjYYn01c5vPvUayU252A06aZLx6ipx0BZunL6WcIsy2ffPP6GSxOMqE0fVV6Om55PdhlrDcPZCNLZ97VN/i7huDfX+9sIB1uuiCkP9k2hvbmO9d";
        $image[]	= "ZQu46Ge7u8Srcn/VNLk+pA3QVfZ9srhbwRKiZSWXDNcuxafYcqI1rzajwNDo700+mbOm0+R5NpQUoddbb10dDgMBmudlHWicviOldpIjpNOlBKLQ0dNDQ0TF3tfVSW3Uxh76U7PT8QLzErroKWPWc7";
        $image[]	= "sGj587GUk1zFlZuP2H4x1udGODXvY4d3v7DkgM057xSG23ze0K239PvlGiodrocwaOaGUTbj4vlkl7GWMBamkO7NrzofbGI6bixM9Vje/jTCQUMDGeb8xCFv6CMcvOY46GqoQy+ivOHBfrfpDjSUic";
        $image[]	= "E0SKtz/1x/Zd+jNGOEuYlXLMcGB4Zo0TjX/Xtxuy+IyA9NkdbHj39UZJN2bFiJ5bNPFmZQb7fzTt/O1l7aOj3V4T6rXjpFjZedL2cFYbRooI2w5PwVWviU83JBdF2Wz+tJ+oJ1RYfHZHr3JM4SfXZx";
        $image[]	= "Tfn0+9SF4tiUvI8cv//FtkZ4tP68wznjMlaLaNCZytrq6Zajr446f3g+2WWsJfRrn3I77UC/7mlpRog+QePih5ybYMsV23N7OqjndBh1hE+jnjN7abC5ymW6Q91tZHjvPnNapp+4jyxpxghPhduG1J";
        $image[]	= "smn3Za2R/enG8553Jxi81nl/JGmo3QF6NEehsnJrk0QUW6xg5a/MxI3yTMprrEcUCCtRAhfjT3bECN8KN3z7iNisOXpAdVBWc0tpJeZ6SWqwZqbtRTY10L1ddco9qqq1RzqZkulzZR5YVGulTUIEz8";
        $image[]	= "Ym4dweyLMmuoIL2a8s9UU27KZcpOqqJzCZWEaDjj5CVKP1FOacfK6MyRUkqNLqWUqIt0OvIiJR26QIkHSyjhQAnF7y8WTcdxYG+R4NQeE7s98+r+rapN8JEzS6hnsM/yfvuG+sUGu86aRmGU1te+V7";
        $image[]	= "zP4ZwPK0+JFWPa+20rGrUmCBbl7w+q74lX3yW9nowtV8nY3EDGxitkrKsiY005GS9fIGNFIRnLc8lYep6MJRlkLDpDxoJkMuYmkCH7JBnOHSdDxhEypEWR4cwhMqTsN/flJX5ChvgwMsR9RIaT28kQ";
        $image[]	= "u5UMx7eQ4dgmMsRsIMOR9WSIXmvu84tabWIVGSJXmlhhJmK5ACutuJ1/N+lb0oywfeckh/y0rn5MjAxtW/80tX88wcRb1L79derYPZ36yzM9pomJ91hVRnm2gfpSv+TdW2nGCPcstZ1gjIEzzvrgjN";
        $image[]	= "dGKgNEjkv+ajavheOO2ZgdJrwr11VYNbtC7YYeyjtdKyJBax3ZWmC5JmZboc1naHItyWigelO61qrIvxowI4SpKH2qrhDzGsMLBbHgk0I6sctEWAEd31lAxz7Op5gdeRSzPY+it+ZS1JYcityUTYc2";
        $image[]	= "ZNPB9efpwJpztG9VFu1ZniFMddeiNNo5/yzBgLfPTqVtM1Pog+nJtHnqado4KZHefyuB1r0RT6tfjaOVpgh6+d9PmvJwQrwrJXJFntHEjObblS+eotWvxNHa1+Np/fgE2jAh0fRHTxJtefs0bZ2RTB";
        $image[]	= "/OSqEdc87Qx/POUtj8NHF/5AP52bcyk/avzqJP154TeUWeIzfniGc4bHqWI9tyxXMd3ZEvnhPPe9z03Hh2UQa7zOURG+6Zl/d94BMTVDSncI8wLfvBMjgX/YLWkaS9Tl8tEp89nraS9H3t4ljK1RLV";
        $image[]	= "JggWHJpE+kPLRsFS0h9cQvpPF5H+wELS759P+n3zSL9nLul3v0P6T2aSPuxt0n88hfQ7JpD+wzdJv/U10m95ifQbnyf9+88Soib96sdJt+JR0i37A+kW/5Z0Cx4g3Xu/JN2795Ju9k9IN/NO0k2/nX";
        $image[]	= "RTf2Dub4PBvHmLpYkRoxvFiidTvk+6aT8ynX8H6d65m3Rz7yHdPFN0Mv9XpFtkin6W/N50jz+SfuVjpns+Ybr3OHMekJfNL5L+g1dJv+110m9/i/QfTTTnG/nfNYP04bNMzzSb9Hvnmp8Rz7p/genZ";
        $image[]	= "F5qf/+Bic1kAlIsJka8gNEL0/znLT/epD8TnMDRMph9tNGc96tRZH2OgpRkjxKAWa53+tMyhkj+xs9jh+h2zzREZmjatlXGsShz/+N00m+OG5i5a/XKc+OzDWWdsPkNEKSruccepVTfyxUDkt3f5OY";
        $image[]	= "sZt9SPjLBCX+Py508GLCL0NCVEGKEpojmpRDd7zJEQoiJESIiUkg5eEJFTsimKQjSFqArRFaIsDLw5F19hisAqRSSGiKwwvYaKs67QhexaKjVFbOUF9VRR3EBVF5uouryZrlRepfrqa9RY20LNDTq6";
        $image[]	= "1mwgXYuRDIbWkIs6FNQ0jf42ZYFTE4QQDeIcmJq1dL3t9IWIpyxpfPfEWw7XYtCM8vm/Rj1DX45xv/egN6w5NNXG6AwRnlhujoAQER1ebY6SEC0dNUVNxzaT4cQH5ogKkVXCLjIk7RajJg1nDpqjME";
        $image[]	= "RjiMqyT5ExL9EUraWQsTiNjBcyyViWTcZLBWSsKjFFeGVkrK0gY0MNGZvqTX/4NpNRrwv675F+jYemUZMR+6sOdSWYm6uVYhQjVCNcq6TTdcSxlcMbDbW1qL6/M2nGCEFP50jHLKIvmwreFFEgkrOX";
        $image[]	= "0k8XvSXf5vjBdTnmvsck2zbw3YszbdK1jvDa9D1Oo9OchBqba+L3XLT53HpBADXSctNoqDLawTLfi33LEq3Z62DtWct5d8VPp5beNnEcTaZ/y1xvkw62WrJXbGPOmI3PHh4s41vQDCsWo3Y5WCYl4E";
        $image[]	= "ZobVgWTBF214mNon9PjfqKT5ue88sirbatLzmdDuRKwz2d1JsVJSby+7o/UVNGeKVUbzmOPjvrzxL2XnR6fen5JvF5eoztiLpNk8x9jNbm6WxeIUxOEeYf4hhGklpr/ZuJNtfATK21b8U5HiyjMUY7";
        $image[]	= "feJUY57TdxtVl0FfjBxncy5GeD6YPJ++cex1h3SSmosc0lhxMdLnRniMp0/4HEPqp6Sb9G276RPfEVGxN9f72giNix+0yQuWP3M3+MWTMLpUP/X75sEx8+/zau6hWMS7JJk6ds+grsPL/bZLhaaM8N";
        $image[]	= "zJastx/KWB/iYcx8/uDudNTugXRHMhpjQowuLd6DPEYBlrJR1wbG61Hq2KtHCs5uKIITfXtDlcg1VrrIXfA2WEgKdP+J/SUUyovzfxHafv1ZkJumN16WGn6fw80fNC2qOllCfU+wUM4hEDcNCEnBRO";
        $image[]	= "xsZaKRPqsSi2dYSqn/I9kwk1e77QTXrGZb//bL7gl2mgptD1uaZor684iTr2zRHzFjGtYlBfr/re3khTRojBKtbatSBDHE+JKLc5bt9EiggNO0EoavrMvOz7DTHi1N7UrAfSKPMOEY0qKstpdrgmZp";
        $image[]	= "ttPrfNSA2oEQKeUO9/vF1ibU1ptMM7Pa+/ZDMIxhPLL0Y4/W5ktJT53AR5ibXgxKdG2N9rEw1i66SxCFszWaZdzPuFpUkUA20GaotFk2dn5BJqXfWo2JJJP+N26jq6VkzMD4Q0ZYQwFGuhOXTVy3E2";
        $image[]	= "o0Hxb/TJWSsvqZb6ekbOuZjVKNLDQBprRW3Ms7kfRpxap425iTje2jLSfq00vVpTdHbkrxuMXLUewKJGvMRacPLw6YVeGUtai2OzPUaPjtUE0Yd4d/wMnxshnkt22TKO+LpptHXN41Y7SNxKvXmxLv";
        $image[]	= "v00IQ52FRBveejqffcYYcIDoNibEfBftvplJG2Dc9Qb+4Jm4W6+y6mUl/pWVVrmHorTRkhRmtab2eEaCz9qG2bdmrUJXEumiwV2W+BhGtwzsaJp22OKyNJFewn4GPKBI43VY9MoocpWl+z/s0Emz0I";
        $image[]	= "sSKO9edqxEYYnCwvOOiVsVR2NDm8U29Xell2wbkJosJ68dxGn5ugMN4C7/qsmMDiayMcaLxE+uk/th29Ou2HImrDQJe2LS9Q69onyPDuvaSf+gPq2DWV+i853yez70KK84FA2Kli5Z/EwJxBXZ3Ddd";
        $image[]	= "iyqdcUTWKKRf/lXJ8+n7U0ZYTm/rmRULqnq1/09ylC9IZ5aO4Gz0CYnI9z0E9oHSmiSVXpW9s+64xIXxHOW/GCOe2CVNsXilGkOI5rrQf0QJEbctkINUpm/QWvjAUT2+1185EXx2SCb+V86BcTBHgu";
        $image[]	= "2WXLOOKPeYRYKq0reiUZF/1GbJGkbJVkeOcual33FHVGLKa+wniv1hXFVktdMatN1ywSxodBMGgadSf0F/aYIkz0GQ40lLs9dyzSnBG628rIessmGGKfi81wrc2pOK3B5rPmK22iadN+Z3nrtA+uzb";
        $image[]	= "H5DNM68pNrbSbzQ5hPCLNlI9Qud5yc6tFYEpsdBw48n/W+22uWXnC+k7e/TRDPI7tMGef4e9FtfLdkLIMm7uthzdKxSnNGaD+HTxEiNqz9aX3u+VPVTs9VJr8r/Y7WC3E7EybZW4/ChLldrXU+J0wR";
        $image[]	= "Jtkrg3nYCLXLikLPu9Q7Wxbtcmcz/XeM86hQlgkCPI/sMmWcI2OHeq1Ic0aIlVHsozUoLabS4VxMj7Bu3lRkb1CxTlakUdRh7HVYvBtgX0PsUuFMqLCO7ShyOpdPjdgIg5crLU0e+/t+EDuRBocdv7";
        $image[]	= "MV7Y1igrwyheKHJyeLyfWuvlP+NkE8B55HdpkyzmEjVC/NGaH9lAaou7Pf0jdoD1aWsRd2mnCY+7c4UyyijSXRIPQ3YrTp2tdcb8GEgTFYk1RZ8QbXog9z13zH9NkItcuM7J0eTSbscqLL99s/NEAd";
        $image[]	= "A66bpAJhggDPIbssGdd4qkOxMgv62gBGeNp8x6pyxPHhgT7RF9cdv03sLIG+P9E/52benythFZn+svRRXydDIW+E9hPDgf18wqMfFro0HqyyUp5rO1E0fFGmy/MxMhVNrK5WZ3EFjFhZ4NsVeBZZL5";
        $image[]	= "HxH5XX6umm6OfdmsyXov9O5e2jnzQcKBNE/vEcssuScY2nOrTn7P6R3SNWPWrzWevKR8VxrNzSeXiZeT1RmGFzpep5hGJQzey7R32dDIW8ETozLTSPYt1PCBvpejIpLHqtTKfAABrsbjAak/MVeBZZ";
        $image[]	= "L5HxL94swo0m0oqORq/fO+YJvnxus99NEKwvjubvWJDjqQ7FRrliQvs7d4mf2GxXkbURwvy6T4eJuYBshKMo/0C8RFeqsJuHp4A9BA+szvbahGCeWHjb2Qa7gaLSrkk3kC+R8S8Go5F+EjfDo+H815";
        $image[]	= "EXaP+VVI+LERcZa+i+pDkBMUHkG/mXXYaMe9zVoVihBXP2jEt/R10nNpgjvoTtls+tjbAn/VMyLnpAzN1DE6pYZ3TRb6h19Z/N8wjXPG7ZkBe7QBjevYf0039Ehrk/o479cy2T4bFYOI5jziF+YnNf";
        $image[]	= "TKFQhDQxTxEb/LbvGO90IW+sR9oRNpmMC34p6EnbL4735hyl1hWPkHHZw2JfRDTtQmJd0l1TRR6Rbscnb1NP6m4yzr+fDPN+Tr3ZMX6rQ/3+Ej0JUyZkmZevcDftw5PYCEOD/MZLXk+UvyPubbF2aI";
        $image[]	= "6+kpp7TCbU3ymaTvfUJFu2ZAoEyC/yLbvsGM+4q0N7UsLN2x6ZTHCg/qK5edRkJIqsjVDZdaIndY/FCLHsGeYAtm15UfwOg4Ewz7DtgxfFxrxKpNmbGSk+E/s/mn7vPPAudeyZaTZUk3Epat/2qrgO";
        $image[]	= "JiXydnStQ76xIo0w18/+MESfJe6JJdiG2syBw0B1gTBhCEaHf8OgsUwc0sa9Yc4DtSXCeP1Vh/r9JXqj2jKDWAINozURDfqKDROS3BrYWNJGXpFn5H0sYiMMHT4pSwiYifkC5Fd2mTHe4a4OxQowZg";
        $image[]	= "ObRl3H1wtjE82jny2F5skIYYAQzEcY2uIHLb/D+LqTdood6kWkeXKz+Ew0jZqiRUX6abeJY+K67nZhWmiCxeLaIm/h0xzzve4p6i+zXRITq9Rgd3trYd9EkRdTpNixa4rlOPKEdU4VYQNkf9Whfn+J";
        $image[]	= "soXd6sPeS3dqhNY7TcgSG2FosSBvn3SD8wbkU3ZZMd7jqg6FuSl7ADpssBu/TZzjyQgRuUGYUC+M0BRpicjs7duEubQu/4MwPXsjVAwTQn+huIcpOsPC20pTLbZ3cmmEqx6l/srzNsdgbFjezVqIAm";
        $image[]	= "Hqjkb4hq0RTrjVb3WoX19iMAmLcSOSszZCRHSyxUYYekzI2ibd6NyB/MkuI2Z0uKpDEXUJowmbTP3lGQL0m4nmUZOBQZ6MEP1t2E+wNzPCHCFu/Bv1V5w3m+S2V4QJdeyZZWuEaBod/zXRdIm+QZgx";
        $image[]	= "mk/RbCnMdMlvxXXY6NfaCLHsmjK9o2P3dOpJ3mXzPINXL5Nhzk/F1k7i/O42EW1i4W82wgAJu0Vg8W1lXdHFz5ygrnbnE+cDJTbC0MSb+YUy4PmCoYmrOrR19WPCaKwXxIZpiGgOzaMttV4ZofVC2V";
        $image[]	= "hfVBjQjNtHtleadaeDEYpFu00RoyUCjdsq+vusN/1VrlOMEP2CyBuEhb8RaaKJs/2j8RZTRL8f+jixYa9x4a9F0yzERhhgwfxiw0rEru72u9sHWmyEocuaoqhRbb7rT5AP5Ed2mTDqcFWHDlwpEhPi";
        $image[]	= "lQhKEaZG4LjYE7ChXOzugDU9h4xNIoIzDzjpESMyYZYD9aXUl39KRGSKcG1fUaIpOjwnjBHn4noI6cHIsKsErsO+g4qQLvr6+i+eEQNgxD1MEaeSpvWEf+QJA10G6i6I6yz3bm0WzzbUaRw51qE3pV";
        $image[]	= "Nt9YxVNp+72sGCjXCMwia88Xtc72QRCLERhjbxNTn09aOvSTVB3B/5kF0WjHpCtQ4NBrERakBshKHP5WsN9EK6+x0n/AXui/vLLgNmbHAdql5shBoQG6F2QFT2s7iZATFA3IejQO3Adah6sRFqQGyE";
        $image[]	= "2uNIVQY9kDjPLwaIdJG+7GdkfAvXoerFRqgBsRFql4LGSno3dzfdHjtlTOaH65EO0pP9TIx/4DpUvdgINSA2wn8MKq7ViZVepmfvpD+nLKMfx06mr8S8TP8Z9Sx9IeIp8RO/4zg+x3k4H9fJzjvjf7";
        $image[]	= "gOVS82Qg2IjZBhGK5D1YuNUANiI2QYhutQ9WIj1IDYCBmG4TpUvdgINSA2QoZhuA5VLzZCDYiNkGEYrkPVi41QA2IjZBiG61D1YiPUgNgIGYbhOlS92Ag1IDZChmG4DlWvoDHC66+/ntra2mSXR8gJ";
        $image[]	= "ZYayk/2fkGEYuXAdqk6+qkN98hIfeeQRCg8Pl10mISeUGcpO9n9ChmHkwnWoOvmqDvXJS8zJyaUbb7xRZIr/qvEslBHKCmWGspP9n5BhGLlwHTo6+boO9emLhDMjTEWbLeMalBHKik2QYRgFrkPl1a";
        $image[]	= "HSXz7DMAzDyER6BhiGYRhGJtIzwDAMwzAykZ4BhmEYhpGJ9AwwDMMwjEykZ4BhGIZhZCI9AwzDMAwjE+kZYBiGYRiZSM8AwzAMw8hEegYYhmEYRibSM8AwDMMwMvl/rcI509pLd2wAAAAASUVORK5C";
        $image[]	= "YII=";
        return "data:image/png;base64,".implode("", $image);
    }
    /* Image For Connect to Alibaba.com */
    function connecting_image()
    {
        $image[]	= "iVBORw0KGgoAAAANSUhEUgAAAcIAAAB4CAYAAABhPvLiAAAACXBIWXMAAAsSAAALEgHS3X78AAAgAElEQVR4nO19CXgVVba1YDOIaDeifi3S9v9QX+vrVlF5iD5tX6u07722VVBwREGaUaAlCh0RBI";
        $image[]	= "IyKiCiDGoYIoMyyRyQISAQIGEeEiQQAiRkuEMGMifsP+vEqtzKHau4956qy17ftz5IVZ1zz629c1b2Gfa5Kj+/4Comk8lkMq9USm8Ak8lkMpkyKb0BTCaTyWTKpPQGMJlMJpMpk9IbwGQymUymTEpv";
        $image[]	= "AJPJZDKZMim9AUwmk8lkyqT0BjCZTCaTKZPSG8BkGuWiRYvpscceo2uvvZauuuoqpmTCDrAH7CLbN5hMPZTeACbTCAcPjqIHHniANm3aRMXFxcSQD9gB9oBdYB/ZPsJkBkrpDWAy9RIRBzrbwsJC2X";
        $image[]	= "0/wwNgF9iHI0OmVSi9AUymXmL4DZEHw7yAfWAn2b7CZAZC6Q1gMvUSc1E8HGpuwD6wk2xfYTIDofQGMJl6iYUZDPMDdpLtK0xmIJTeACZTL1kIrQEWQqZVKL0BTKZeshBaAyyETKtQegOYTL1kIbQG";
        $image[]	= "WAiZVqH0BjCZeslCGFokJiZSTEwM9enTh6Kiomju3Llkt9t118NCyLQKpTeAydRLFsLQ4PDhw9ShQwevWWPGjBlD1dXVAdfHQsi0CqU3gMnUy8sVwkuXLpHT6aSqqipd5crLyw1t4s/Pz6fKykrd5c";
        $image[]	= "KJ7du3U/Pmzf2mUevcuXPAYshCyLQKpTeAydRLo0IIIRs2bBjdeOONopO+5ppr6I033qDs7Gyf5fbt20dPPPEENWjQQJS78847xXChL0Asxo0bR61atRJlmjRuTC+88AKlp6cbansokZubq74T5b30";
        $image[]	= "7t2bZs6cKb5D27ZtNWI4evTogOplIWRahdIbwGTqpREhhDA9/fTTamd+/XUN1P+3adOG8vLyPJbbtWsXNWnSRDzX4FdX09XXNFXLYR7NG1577bW6z2rSlBr+IqI333QTnTlzRnf7QwnMAypthXCnpq";
        $image[]	= "Zq7iOCdn2mWbNmQjz9gYWQaRUGtbJly5bTb3/7W+lZ8K1CvCu8M9lOYDUaEcL58+eLd35jy4a0acVNVHahNR3f/Vu670+NxPUBAwZ4LHfvvfeK+62ff4r+lhJPz2UkUNsJQ8S1hg0bUlpamluZDRs2";
        $image[]	= "iPvNGzehta++TTRqBmW9O54e//2d4nqXLl10tz9UgMjdcsstqk+uWbPG63Pt2rVTn5s9e7bfulkI9ZP7UDl9aFAN2KhRI5o3bx4VFBQE+/c14oB3hHeFd8ZiqNNpDQgh5rZQbvrE3wgRVLh3083i+q";
        $image[]	= "233upW5tSpU+Je45a/ob+nbaLnz21X2bpTR3FvypQpbuX69u0r7o35y9+FCCrMjBonIkMMk+qdnwwVMCysdCotWrQQgucNkydPVp/t37+/37pZCPWR+1B9CGYfGjQjQpnRKIY+4J3h3cn+JbQSjQjh";
        $image[]	= "448/Ljrm+KU3aoTQcap2Dg/zYvWRlJQk7v3mnn/XiCB4V9Rb4t6IESPcynXt2lXcW/xiT40QgogScc8sJ2ecPHlSFbc77rjD57NKVA1269bNb90shPrIfagxBKMPDWrn5OmvGPzFiUUCoLIo4cCBA/";
        $image[]	= "TUU09Rv3791NV0K1asoD//+c80adKksL5E2cA74w5Dv6/phRKl9XrjWo0Qzp7aQly///773cpgZenVV18t5gaf3BqniuCz6Vvo+rtvF+UWLlzoVm7kyJHiXqe77tOIIIZJvUWfslBUVKQuAsJf1ljh";
        $image[]	= "6g3vvfeeKoRDhw71Wzf7tX6/5j5UP4LRh4a8c3r//ffVXx78H+jYsaN6benSpeLaDTfcoF4z48q6UII7jOD4mi8cOnRIiJqIZro2o8Vf30DR71xHjRvXikBsbKzHct27dxf3m9zcku79aDC1mz6SWr";
        $image[]	= "a/VxW0kpIStzIZGRnUrCbCxDMv3H0/LenSi8Y++ZwaDWIlppnw0EMPqb970dHRHp/JzMwUQ6fKc/Hx8X7rZb8Ojl9zH+ofphdChK2KcZSwf9CgQeJndEwHDx4U1x555BFxDcu4zTJsFC5whxEcX/OH";
        $image[]	= "OXPmqGLoyoEDB3otA1989NFH3crATzF06g0//PADXdO0qVu5V155xTTzgwri4uLU9iE6RERbWlqq3k9OTqa7775bfQb/D+Q7sF8Hx6+5D/UP0wshkJCQIKgAv0QrV64UmSwUILxdsmQJnTt3LqQvzI";
        $image[]	= "zgDiN4vuYPKSkpYlgPi2cwrOTql94Af12wYIHYc4gVn4joAkk5hm0Sw4cPF5/Vq1cvWrduneF2hxL1t5aA2Fzfvn17MW/oeh3Dp9h8HwjYr4Pn19yH+oYlhJDhG9xhsK/JBjpR1+E2T8T+wWXLlgVc";
        $image[]	= "J/s1+3W4YCoh/Pbbb5kGyB0GdxhmAKIM7A90HQYFkWcUCQKwwlQP2K/1+7XsvsiqNJUQYlMyUz+5w9Dva4zQAotj9uzZQ8eOHdPMF+oB+7V+v5bdF1mVphJChjFwh8G+Folgv2a/DhdYCCMA3GGwr0";
        $image[]	= "Ui2K/Zr8MFFsIIAHcY7GuRCPZr9utwgYUwAsAdhn5f85UTkyEfsA/7tX6/ZhgDC2EEgDsMfURewaysLNlmY/gA7MM5dPWR+1DjYCGMALAQ6uObb75J06ZNk202hg/APrCTbF+xErkPNQ4WwggAC6E+";
        $image[]	= "HjlylFq2bElbt26VbTqGB8AusA/sJNtXrETuQ42DhTACwEKon1u2bKHWrVuL44Dmzp0rMp4w5RJ2gD1gF9hHto9YjdyHGgcLYQSAhdAYs7NzaNas2eKIpddff10cS+MrRRgzNMR7x/uHHWAP2EW2b1";
        $image[]	= "iR3IcaBwthBICFMDhEEmLZonAlEu9dtu0jgdyHGgcLYQSAhTA4ZCFkIbQyuQ81DhbCCAALYXDIQshCaGVyH2ocLIQRABZC48SZf6tWraaFCxfRsGHDpIvClUi8d7x/2AH2kO0TViX3ocbBQhgBYCHU";
        $image[]	= "z/PnM8VBuddddx116tSJevTowZRM2AH2gF1gH9k+YjVyH2ocLIQRABZCfczJyaUHH3yQoqOj6eLFi7LNx3AB7AG7wD6wk2xfsRLN1odeqiijitSdVLz6Uyr88i1yfPgY2Qf/kWx9WpPt7TbkiP5PKp";
        $image[]	= "j6CpWsn07V+TlS28pCGAFgIdTHUaNGUc+ePWWbjeEDsA/sJNtXrEQz9KGXqquo/OAGKpzVh2wD7yBb71Za1ohgwbTXyf7uPdrr/f8flfw4S1q7I0oIK8qrKOdsIWWmOS3B3HOFVFlRJd2IVxrvuusu";
        $image[]	= "cWAsw7yAfWAn2b5iJcoUwurifCpeM5kcQx9wFz9X9vs9le5YVCOY1VSRtpeKFkSTbUAb9X7pru+ltD8ihLC8rIrWfXOEPnptHY18cbWliDavn3OUKsqMCyILoT42atSIKioqDL9vRugB+8BOsn3FSp";
        $image[]	= "QhhJfKS6h45SSyD/p37+I34HbK//RFurjsIypLXkVV2WmaOqqdF8j58f+KZ50j/zvs3wGwvBAiovp62A7pgna5/Gb4jprvUi3FiFcazTCExPAP9mtz+3X5wXhyRLfzOPyZP6kTFa+bRhWn99Olqkq/";
        $image[]	= "dVWkJf0SMd4Whpa7w/JCuH35yaCL0tS3N9P8Mbsp5uU1YRXDn1aclGLEK40shNYA+7U5/RpRYFHc0HpzfP9GhTN6UlniUqq+6NBdZ/nhTaIee9SfQtBi/7C8EH7+z62qkCyasJeWTdtPY15da1iMvo";
        $image[]	= "hKUOftzv3sCKsQTn9nqxQjXmlkIbQG2K/N59dY3en86GmNCIo5v9Kiy6q3YMpLoq6i2EFBaqk+WF4IR79UG7Vt/f6Eem33utOGxehYovbA1k/7/Bg2IUQEGi4jZp+30a51P9PGBUcoMf5nysmyXzGd";
        $image[]	= "DguhNXAlCaHzwnlybPmWHMsnkmPzPHJmZej+7qH266q8DLHlof5QaNk+936rutBGl8qKA6q3dOdidUi18pycRWyWF0JFRNIO5qrXcs8XGRKiSf/YSFVVdfN09gsXNfcxZHow4RxdLCin6qpL5Mgppt";
        $image[]	= "1rT4ty3upEdLp5UQrlZRZRdfUlKi4sp9SkbIodsdPj8+Ew4u4NJ+nj17ULi8Z2W0fJW09dER0PC2HokJGRoe4DxAnzt99+u9go//3339OlS5d01XWlCKFj53KyDfqDVmAG3kmOrQt0ff9Q+nWVI5Mc";
        $image[]	= "77d3E0HMEVblnnF7vrrILqK8S1W+F6VVZqaKxTSo6+LCYaFqvl9EjBDu25ShXquqrKbRXb3P722Yf0xEfhiKdL2+5qvDmrrXxR5V780dtYvKSjxP+l7ML6Mv393m9jkTesRT1ul8r23HatFwC+HRvR";
        $image[]	= "k0qovn94LoOvXA+YjvfFgIQ4OpU6dSkyZNvKZSa9++PZ07dy7g+q4EIXQe2UG2vrd5Xm1ZEyE5928K+B2Eyq8xJ+iM6ehZBPMytM+WFlHpllgqmhdFpdu/parsU17rrS4pIMeIx2rrqvkXnyMLESOE";
        $image[]	= "8fO0IfW0QVs8dvbLPz+gPnP6SJ7m3s/767Ib4K9XJdL7bMBmryKowJZVRDEv181NQmzSj9p8lkGE+NWwn8IqhF99sN1nVDxvzM6I73wup8Oorq6mHTt20Lx58yg+Pp5KS0sDKpeXlycOn42Li6Pjx4";
        $image[]	= "8H/Hl79+4Vn7V69WoqKrq8eZhQYuzYsQHlFb3tttsoJyewLCJXghDap77mc9+dfUIn6UJY+M1At3blT3xOrAwtmPwSFX79dg37U+Gs3lQ0/12qOJHot05svEdWmdro9w6qPJ8SkrYHiogRwriPdmuu";
        $image[]	= "Y+GMpzk4Z27dXx2IHMe8Uiteo7qu1ogdNrwr5U66DLsChY5S2r/lrIgEXfHDlwfVMitnHNLcw5Dr0V2ZdL6mXlecPJATViH0txIW+xp/XHSUtq1IoT0bT1La0Sxy2PMjqkMy6mspKSl0zz33aDp2DP";
        $image[]	= "+tXbvWZ7nx48dT06ZNNeUwXJif7320AEOMDz/8sKZMixYtaMGCBYbaHkokJydTgwYNNJEf3smFCxeE6H/44YfUuHFj9f7zzz8fUL2RJoROu52cKUnkSFhIjvUzybHiE8/ZV+ptQA+1X/sC5v88task";
        $image[]	= "/gtxH4KGzfR6oznXVaee5hjDjYgRQixqccWWxalunfzab464lZ8dXRuRYWjTFbtWnxLXv/5gh+a6I7uYJr61QdybOXS75h4iSjHE2HUN5dvqHAOR37dj96hinHe+7i97zDWO7bbeVEK4NvYgLZueTN";
        $image[]	= "+OT6TPBm4S85xzR++kvZvSyG5zWr5zMuJr9ppOrFWrVuJd/+53vxOJodu2bSt+RieflJTksdznn3+uCsBf//pXevnll6l58+bqz56AKPMPf/iDeObmm28Wp7d36NBB/AzB2bBhg+72hxKdO3dWv+OT";
        $image[]	= "Tz5J5eXlbs+gzQ0bNlSfO3LE/XexPiJBCJ22PHJs/47sk18R+TVtwx8l+/QeZI+NIvviUaYWQoibt0wxihAaAcoq9RT/MMFQHdUFeYY/3xMiRgjB0ot1E7OIvjQdfE1njkiuPpR5uhXTD2iuf/dpcu";
        $image[]	= "3c42btGPj8mERNva4RXoG91GN0mvzjGU2ZjXHaoTHXhABGEOqh0ZwLdkpc/zPNHradJvf9kfYlnLZ0B2XE12JiYsR7hiApiboxTNqrVy9x/W9/+5tbmcrKSrrhhhvEfQxvKkhLSxPRHa4nJCS4lZs1";
        $image[]	= "a5a4BzGEACtQjolq166d7vaHCsgA06xZM1XgUlNTvT770ksvqc9hKNUfrC6EYhFMdHuyjX+OHFviyJntfqKGmYdGXQVLZY2YF6/9TMzvGUH5kS1k63OrqKvgyx66FlBdKr1IZbuXiY38wZ5PjCghzE";
        $image[]	= "ip6zQwZ+d678dvPc/LpOy9IO7vXKlN+zNtYO0co6t4etpXCJFTgP2HuIaVpK6Y3HeTpgzE1BULxu2xzGKZQ7vO0JS3N4loMTfbmlsujPhax44dxXvGXJ0rcnNzxfXrr7/ercyhQ4dUQauPIUOGiHtj";
        $image[]	= "xoxxu4cIEPe+/vprzXVEWspilEDnJkMNDOEq4oZI2RfwfZRn33rrLb91W1UIndlZIuqzffAIOZLW+/wOZl4s44x5StMepD/ztfjFH7C61P7O3bWLYz58LKC9hyKJ99GtVDT/PSpePjZkp1RElBDuWZ";
        $image[]	= "+uXsdfGmPfqB1yxL8lRe7DNQDmBTFciC0NCpC8G3OGWCzjis2L3IdbXVeroi5cO3O8TpCzzxS4lUHWGlfg53AJIXi52ydseU5a8lkSTR+8hTIz8izXWbEQBg8shFo6z6WRbeRfyP71IMKwaCBlzLh9";
        $image[]	= "AkmxxXFJSmT6z7tqRCjbf0Ef9Tk//p9fBP5WqjxzyPuzNdFe+ZHNVLTgfbFvEdsqquznDX92IIgoIcRiFVfMGblLXE9YckJzvf4QKSI0nASh4MIv4lV/3hArTuuLmutCGmXfIaJRBanJ2W5lVs7Qtn";
        $image[]	= "PGe9vCKoRgMDbUx88/LOYQs85aSwzDNTSKYUMeGq1DpA+NOs+frokC/4scS8fpbrfZNtTjLEFXYcbRSZcDHM2kbrsY/qg6JIqFNpVnj4ghz4tLx1D+hGfFkUz29+6j4lWfiI354UBECSEExRUYDp3w";
        $image[]	= "1gbNalD8H3Nyrti/+SyVl9Y9c3x3lqgPC2lcseyz/ZrPw4pT17qxNxHX8/Pqxq+VoVdXHv6p7q8brFx1XcBiBDI7jHXzDtX8wbCV8nIclum0rLZYplu3brxYxuR05maTLaYj2b//SFqbgz00mj+ps8";
        $image[]	= "sJEm2obP86r3N6GMKsunCSyvauoLI9y90iOCyKqR/xisVD9YaDC6a+TGX71moSdZcf30blKT8ZymEaKCJKCLFa0/U4I0RjO1dpx7S3LftZPIshSwX1j0BCGTzz2YAtmuvKSlKF9TfgY8sErl9Ir1sW";
        $image[]	= "D1F0LYPFJq5nECIjjut9I5DdYXw3eS8tGJ9omU7LqK9hKwBvn3AHb58ouEqsBJ3VX2p7gy2ElVk/k/3de7WLd6L+JKI2LHQpmP4m5X/yAjk+eITs7/wHFc15hyp+3u2xrvJjCV7nQfPH/10szKmyuS";
        $image[]	= "dbwJFNZTXRJLZYVJzeF9Tv54qIEsLa+bm6ULq0uELM9ylA9Da+e7zPxTMANufjGcwTukaKGFJV5tZmDd0u6leA58a9WVv3wW1ag2IVKa6jrOuCHmDp1H2WF0LMGX45JIE2f3/MEh3X5XQYyob6uXPn";
        $image[]	= "6t5Qv3TpUt5QH4Eb6h2rPiPbmKcDnhM0o197A1KlFa8YT87RT4ojkpSjkhz/akf5n3ahi0tiqPzQxoDyiuKopeKVE2vKjBbCh0UwGBr1BcwXltZEmJgzrMw84fPZy0HECaGvo4xcj2yCIJZ7OQzXVZ";
        $image[]	= "yO7MjU3MvOKBBDm/VPlnet+7tPkjX3sK3jwNazms38APYTQmytLoTg2VM5NL5HPJ04aP4UbaHoMBhXZoo159FdZBv8J3KmH5fe1lD7NYZFZaRBE5/rJ2fp5SLihLD+Hj4FiNiQ+9P12b3x6R6fVTa/";
        $image[]	= "K/OOrom4PQGb7F1XYULccs4W+iyDTfbKYp5IEEJwz49pNPXtTWTLNfd8IQth6HAlJd125uXUbpHYttgU7WS/No6IE0JkRqkfrQE7Vqa5PYvtEa7DmwrqC9Q6DxlpFBQ5y9ySd4M41xCnVHgCOoTVsw";
        $image[]	= "973MtnBGbqMBZN2kM/zNhnmvZwh2FdmMmvPdE+/19kn9HbNG1kvzaOiBPC+lsagJKLFercYH0is0x94KQJt71/MYkiiTZSogGYb8Rq0096eT+CCQtjkJNUyXiDspjDnPOhe/2RIoTZmTaa+I8NdCzp";
        $image[]	= "rGnaxB2GNWEmv65P5/7NZHuvLTmzzOPn/vwamVkw1wZihacrKk4li+uXKsvFXFzJxhniZAnM/Yn5OR/7/rwBWWQqUnfqLicDlhfC+hvDwfr7CVfNPORVeJBl5cQ+7UbReaMTvT6PlakYYvWWncUbIc";
        $image[]	= "RKgm9vxHeRYcRgExvzp/T/0bRDpCyE1oDZ/FqhGBId9jBhI7zstujx69KfFtadHjHhWc29/PHPiuvI3HJx+ce1+UQhhtlphvcRikU10f+pu5wMWF4IPYkWhkeR9xPAQbr+RApJr5XtFFhAM7HnBl0i";
        $image[]	= "Fyziu8gwYii4cNJu+mHmftO163J8jRFemNGvQfv8aLJ/2ct0bfPn1zgoV2xo/1c78S8O21XgKoQQv5ItsWIvIAthgO9edud0st4+PIU4Q3DRxKSARQjiicTbng7YDRfT6g3phsuIoaAyRHpkt/4MGa";
        $image[]	= "Fmo0aNREYUhnkB+8BOsn2lPh3JG003JKrQVx+KDC0id+lHT1Px2qm1Ed+Ps9T7rkJYunMxOUc/IfbuYQhV5Bkd/STlT3y+dh/hpM7qgbw4BcLxwcNkf/cecgzrQEULh6mb4W19fyeuY88h/sXhvthC";
        $image[]	= "oQB1Yp8iDvgtnN3PYyJv5CMtih1EzpGPC5buWCiulyWvovxxz5Dz4/8V5yJiaBcQeUnnvCPaiHqL5g6m0m3zyfnhn8kx/L+oLGmlx/djeSEEsGVClngFi762ffiDGYUQPLTzDE3qtZGQzk12W1x511";
        $image[]	= "130bFjx/y/WIY0wD6wk2xfcSXSntmGPECOvetM1S6FvvrQ0oR5tcce1Yhg5fnjtcOjNUKiwFUIlVMnSrfFqUKItGfYA1gwvbv4GQIDYJ9hwRfdxcG8SqRZlrhU3IMQ4ueLiz6gorghtYJaI1wKCmf8";
        $image[]	= "Q5SDSIm2rfrErd3ISCPE9ZcVx5izxGciBVt1QW3gUJl+UIgwAKHD/yHQSBOHuvHZEOfKs0eF8HpCRAghcDbVIVKgYbUmosFgcerbm30K2OXUjbaizWj75cCsQgiu+uqAOMvQ6TTP4b6jRo2inj17Xt";
        $image[]	= "Y7Z4QWsA/sJNtXFMJ/7ZNfJvuC4aZpU3366kORAaZWwKKoeM1kIWxiePSXVGj+hBACCEB8hKDFPKX+DOEr2fyNOKFeRJrrPxf3xNBoTbSowB71R3FNlCspFKKFIVgk1xZtmxfl3u5Pu1BFqjYlJrLU";
        $image[]	= "4HR7V+DcRNGWmkixaM4/1etoE/KcKrD1/zeP7ydihDCUwGn1sSN2ehRC15MmZMHMQojT7We/v42Qk1R2WxTm5OSKfW7Y76Yk0GaYA7CHsg8RdpLtKwqRQ9Q27u+EU+Zlt8UbvfWhEDflDEC3A3Y3zh";
        $image[]	= "DP+BNCRG4ANtQLIayJtERkNviPQlzyx/6fEL36QqgIJoD5QvEZNdEZEm8rQ7U43smrEE54lirS9mquQdiQ3s0ViAIh6u5C2EcrhG+38fiOWAh1AMm4Ecm5CiEiOtkwsxCCOJ3ik94bxSkXstui8Pz5";
        $image[]	= "TJE4+7rrrhMbvnv06MGUTNgB9oBdYB/ZPqIQp0LY/tWOcLqE7Lb4orc+FFGXEJrYQVRxYpcg5s3E8GiNgAH+hBDzbThPsCxxSW2E+NlrVHFyb61IzugpRKgobqhWCDE02u/3YugSc4MQYwyfYthSiO";
        $image[]	= "mYv4pyOOjXVQiRdk3Z3lE0/10q3TpH832qck6T4/2HxNFO4vmSAhFtIvE3C2GYgNMikHxbySsa8/JaKi70vHE+XDC7EIKnjmfRhLfiaf92c3Um6enptGrValq4cJF6zBEzvMR7x/uHHc6cOWMq/3Ds";
        $image[]	= "+oFsUfeQ84S5k0SA3vrQ/InPCaFxTYgN0RDRHIZH884GJISuibKRX1QI0Hv31R2vNPQBNyEUSbtrIkY1At3wpZjvcz30VymnCCHmBdE2AIm/EWliiLPwq36qKGLeD3OcOLDXOeovYmgWYCEMMyB+62";
        $image[]	= "KPilPd659uH25YQQjB1APnRT7SPRtPmrK9K1eulC4KVyLx3mXb3hNxMK7II3pkhynbV5/e+tDKjMNiQ7wSQSnA1ghcF2cCZp4Qpzsgp2e184KI4GoXnJSKFZkQy8rzKVR+IF5EZApQtvzwpprocI8Q";
        $image[]	= "RjyL8gDqg5DhVAmUw7mDClAv5voqjm8XC2DEZ9REnEqdrhv+0SYsdKk8d0yUUz87P1t8t+qLzrprRfaaetJdvuMpzX1vJ1iwEF4mcAjvxrjATxMIBawihGDa0Sz6tM9GWj//cEALaLDi9HBieKIEFs";
        $image[]	= "LIFkJHUnxAB96KhTHff1w7HJqSZJnfLav2oWYAC2EEwEpCCGLOcFb0NrGI5tTxCz7bfuZEthiCnj1sOx3dG9q9WyyEkSmEIh3a+NrhQeepIz4/y3liv1gUYxv7jOnnBOuT+1DjYCGMAFhNCEWH48in";
        $image[]	= "hGXHaVz39bT8i32UXiN4np5LT72gWZwU++EOSt1/LijfNzs7h2bNmk19+/al119/nV588UVx6jwzvMR7x/uHHWAP2CUoPnb4J7JP6qJZJek8edBj3c6Th8g+5z2yvfMf5Fj7BTkdTsv9TnEfahwshB";
        $image[]	= "EAKwqhwsyMPFobe1DMHX4z4idxuG/asSx12BQRo6dtK3NjdtLPh42vLNyyZQu1bt2aunXrJg7ZXbZsGVMyYQfYA3aBfYza1nkskexTXvW4XQARn3imxr+cqcmEQ3XtE18Qc4H2xaPIeS7Nsr9L3Ica";
        $image[]	= "BwthBMDKQqgQp9wnbT5FSz9PFqd2jHl1LX3+zhb6evhPPhMaxI1NFMKp57OOHDlKLVu2pK1bt8o2HcMDYBfYB3bSY1cIm33amx4FUKF9YmeyffjfZHv7drJFtyf7N++Q46cl5Mwzz55F7kPDDxbCCE";
        $image[]	= "AkCGF95mbbRcSHCNGrEHZZTQsn7tYthG+++SZNmzZNttkYPgD7wE567CqE8MteXjePi6X6NREgIkZntj6fsQK5DzUOFsIIQCQKoUKIYX0BHNV1NX03eS9h/tBInTg5PSsrS7bZGD4A+8BORuyLeUD7";
        $image[]	= "rP5qrkvN0Ojx3RH7u8J9qHGwEEYAIlkITxw8rwog9mwunZZEGSc9L6zR42tKEl+GOQH7XK5fO08fE0OfyG6iCuHRXRH7u8J9qHGwEEYAIlkIsQk/5uU1tPzLfXTudHDmcdjXrIFg+bXzzAmyzx0iEk";
        $image[]	= "1bZXM8+3V4wUIYAYhkIbxwzkZYWcodxpWHYPs1VoQ6M82Vwo392hxgIUcYuv4AAAXASURBVIwARLIQcodx5YL9mv06XGAhjABwh8G+ZjZkZmbSnj17xAG7paWl/gt4APs1+3W4wEIYAeAOg33NDKiq";
        $image[]	= "qqLZs2fT3XffrUmhdu2119Jrr71GJ0+e9F+JC9iv2a/DBRbCCAB3GOxrslFQUEAdO3b0mVO0WbNmIoNMoGC/Zr8OF0wjhM2bNxe/TAx9wDvDu5P9S2glXk6HkZKSQkOHDqXOnTtTv379KCEhwW8ZRE";
        $image[]	= "oLFiwQB8526dKFxo0bR3a73W85nM03fPhw8Vm9evWidevWGW53KIGtDk8//bRG9OCT7du3pzvuuENzvVGjRrR9+/aA6mUh1EfuQ40hGH1o0Iz4zDPP0Lx582S/E8sB7wzvTvYvoZVoVAjnzJlDv/rV";
        $image[]	= "r9winYEDB3otU1hYSI8++qhbmZtuuomSkpK8lsOJDNdcc41buVdeeUUIq5kQFxentq9BgwY0cuRIzbxgcnKyZrgU/w/kO7AQ6iP3ocYQjD40aEZMTt4n8guiUfxXjX/gHeFd4Z3h3cn+JbQSjQjhoU";
        $image[]	= "OHVBHs3r27GOJDtNakSRNxLTY21mM5PIv7t9xyC33xxRe0aNEieuyxx8Q1JJcuKSlxK5ORkSGGEfHMCy+8QEuWLKGxY8eKv1pxDRGlmfDQQw+pIhcdHe3xGSyeadGihfpcfHy833pZCPWR+1B9CGYf";
        $image[]	= "GnRDQpmVX3imd+Id4V2xCBpwWgNCiCOCUA7/ugJRIq7ff//9bmWcTqcQTwwHYkhVQWVlJd13332i3MKFC93KIaLCvU6dOmmur127Vly/9dZbdbc/VCgqKhJRoDLsmZ+f7/XZIUOGqP6L4WV/YCHUT+";
        $image[]	= "5D5fSh0g3PZOqlESF8/PHHxS8PjgdyRXFxsbiOYcz6wNAn7j344INu90aPHi3ujRgxwu1e165dxb3Fixe73VM6OAy5mgFYCap0LJgP9IX58+erz+K4JX9gIWRahdIbwGTqpREhxIIVlJs1a5bm+sGD";
        $image[]	= "B71GaadOnRL3br75ZiorK9Pcw0G0uDdlyhS3ckr0OWbMGM11DC82bNhQDMeaZZ4wOztbFTcMffrK4Tp58mT12f79+/utm4WQaRVKbwCTqZdGhFCJZrDIRVn1CKHDkCiuDxgwwGO5e++9V9x/9dVXRR";
        $image[]	= "RXXV0t9tphOPHqq6+mtLQ0tzIbNmxQh24wHArgNAYlKsXKU7MAwteqVStV4NasWeP1uXbt2qnP4R34Awsh0yqU3gAmUy+NCCEEzHWLwK9//Wt1bqxNmzaUl5fnsdyuXbuoadOm6hwaNpcrdcTExHj9";
        $image[]	= "PGxAV567/vrrRSSoRJfYVmEmREVFqW2FKKampmruQwRdn8FCoNzcXL/1shAyrULpDWAy9dKIEALl5eU0bNgwuvHGG9UOHXsDMTzoC/v27aMnnnhCFbM777yT5s6d67MMhBerQ5Voq3HjxmIFaXp6uq";
        $image[]	= "G2hxIQNeWdKPOlvXv3ppkzZ4rv0LZtW80iBcyPBgIWQqZVKL0BTKZeGhVCBYhwsCJU7zwd5gmNLHLBSkysNDUzMFwcyEpFzLVC5AMBCyHTKpTeACZTLy9XCBmecfjwYerQoYNHAcSQMBb/BCqCAAsh";
        $image[]	= "0yqU3gAmUy9ZCEOLxMREMf/Zp08fMTeIYeBAUsrVBwsh0yqU3gAmUy9ZCK0BFkKmVSi9AUymXrIQWgMshEyrUHoDmEy9ZCG0BlgImVah9AYwmXrJQmgNsBAyrULpDWAy9RIrGJEjlGFewD6wk2xfYT";
        $image[]	= "IDofQGMJl6iWOQNm3aJLuvZ/gA7AM7yfYVJjMQSm8Ak6mXixYtpgceeMA0JzgwtIBdYB/YSbavMJmBUHoDmEwjHDw4SnS2iDx4mNQcgB1gD9gF9pHtI0xmoJTeACbTKBFxYPjNNRE2Ux5hB9iDI0Gm";
        $image[]	= "1Si9AUwmk8lkyqT0BjCZTCaTKZPSG8BkMplMpkxKbwCTyWQymTIpvQFMJpPJZMqk9AYwmUwmkymT/x9+wFw+26pI8QAAAABJRU5ErkJggg==";
        return "data:image/png;base64,".implode("", $image);
    }
}



