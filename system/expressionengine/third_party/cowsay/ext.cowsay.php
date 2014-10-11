<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Cowsay Extension
 *
 * @package    ExpressionEngine
 * @subpackage Addons
 * @category   Extension
 * @author     rsanchez
 * @link       https://github.com/rsanchez
 */
class Cowsay_ext
{
    public $settings       = array();
    public $description    = 'Add a cowsay command to eecli.';
    public $docs_url       = '';
    public $name           = 'Cowsay';
    public $settings_exist = 'n';
    public $version        = '1.0.0';

    /**
     * Constructor
     *
     * @param   mixed Settings array or empty string if none exist.
     */
    public function __construct($settings = '')
    {
        $this->settings = $settings;
    }

    /**
     * Activate Extension
     *
     * This function enters the extension into the exp_extensions table
     *
     * @see http://codeigniter.com/user_guide/database/index.html for
     * more information on the db class.
     *
     * @return void
     */
    public function activate_extension()
    {
        // Setup custom settings in this array.
        $this->settings = array();

        ee()->db->insert_batch('extensions', array(
            array(
                'class' => __CLASS__,
                'method' => 'eecli_add_commands',
                'hook' => 'eecli_add_commands',
                'settings' => serialize($this->settings),
                'version' => $this->version,
                'enabled' => 'y',
            ),
        ));
    }

    /**
     * eecli_add_commands Hook
     *
     * @param
     * @return
     */
    public function eecli_add_commands($commands)
    {
        if (ee()->extensions->last_call !== FALSE)
        {
            $commands = ee()->extensions->last_call;
        }

        require_once PATH_THIRD.'cowsay/src/CowsayCommand.php';

        $commands[] = new eecli\Cowsay\CowsayCommand();

        return $commands;
    }

    /**
     * Disable Extension
     *
     * This method removes information from the exp_extensions table
     *
     * @return void
     */
    public function disable_extension()
    {
        ee()->db->delete('extensions', array('class' => __CLASS__));
    }

    /**
     * Update Extension
     *
     * This function performs any necessary db updates when the extension
     * page is visited
     *
     * @return  mixed void on update / false if none
     */
    public function update_extension($current = '')
    {
        if ($current == '' OR $current == $this->version)
        {
            return FALSE;
        }

        ee()->db->update('extensions', array('version' => $this->version), array('class' => __CLASS__));
    }
}

/* End of file ext.cowsay.php */
/* Location: /system/expressionengine/third_party/cowsay/ext.cowsay.php */