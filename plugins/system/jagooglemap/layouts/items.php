<?php
/**
 * ------------------------------------------------------------------------
 * JA System Google Map plugin
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

defined('JPATH_BASE') or die;

$field 		= $displayData['field'];
$attributes = $displayData['attributes'];
$items 		= $displayData['items'];
//$value 		= htmlspecialchars($field->value, ENT_COMPAT, 'UTF-8');
$value 		= $field->value;
$id 		= $field->id;
$name 		= $field->name;
$hideLabel 	= (bool) $attributes['hiddenLabel'];
$label 		= JText::_((string) $attributes['label']);
$desc 		= JText::_((string) $attributes['description']);

$chunks = array();
$chunks[] = array($items[0]);
$chunks[] = array($items[1], $items[2]);
$chunks[] = array($items[3]);
$chunks[] = array($items[4]);

$width 		= 90/count ($items);

$field_items = array();
if(is_array($value) && count($value)) {
	foreach($value as $f_name => $f_items) {
		if(is_array($f_items) && (count($f_items) > count($field_items))) {
			$field_items = $f_items;
		}
	}
}
if(!count($field_items)) {
	$field_items = array(0 => null);
}
?>
<div class="control-group map-items <?php echo version_compare(JVERSION, '4', 'ge') ? 'j4' : 'j3'?>">
    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery('#get_loc_loading').hide();
            jQuery('a#getLocation').on('click', function(e) {
                let availAddress = {}; // {index of td : value}
                jQuery('.jalist .ja-item').each(function(i) {
                    var $item = jQuery(this);
                    let address = $item.find('input.location_name').val();
                    let _lat = $item.find('input.location_lat').val();
                    let _long = $item.find('input.location_long').val();
                    if ((!_lat || !_long) && address) { // only if no lat long.
                        availAddress[i] = address;
                    }
                });

                if (Object.keys(availAddress).length) {
                    jQuery('#get_loc_loading').show();
                    jQuery.ajax({
                        type: "POST",
                        dataType: 'json',
                        method: "POST",
                        url: Joomla.getOptions('system.paths').root + '/index.php',
                        data: {
                            'option' : 'com_ajax',
                            'plugin' : 'jaosmap',
                            'address': availAddress,
                            'group'	 : 'system',
                            'format' : 'json'
                        },
                        success: function(response) {
                            jQuery('#get_loc_loading').hide();
                            let addr = response.data;
                            if (!addr.length) {
                                return;
                            }

                            let address = addr[0];
                            for (let key in address) {
                                jQuery('.jalist .ja-item').each(function(idx) {
                                    if (idx != key) {
                                        return '';
                                    }

                                    jQuery(this).find('input.location_lat').val(address[key][0])
                                    jQuery(this).find('input.location_long').val(address[key][1])
                                })
                            }
                        },
                        error: function(data, text) {
                            jQuery('#get_loc_loading').hide();
                            alert('AJAX ERROR');
                        }
                    });
                }
                e.preventDefault();
                return false;
            });
        });
    </script>
    <div class="jaacm-list <?php echo $id ?>" data-index="<?php echo count($field_items); ?>">
	<?php if ($hideLabel): ?>
      <h4><?php echo $label ?></h4>
      <p><?php echo $desc ?></p>
	<?php endif ?>

    <div class="jalist">
      <?php $cnt = 0; ?>
      <?php foreach($field_items as $index => $v): ?>
          <div class="ja-item <?php if ($cnt == 0) echo 'first'?>" style="display: flex">
            <?php foreach ($chunks as $key => $itemValues): ?>
                <div class="<?php if ($key == 0) echo 'first'?>">
                <?php foreach ($itemValues as $key_ => $itemValue): ?>
                    <?php
                    $itemValue->id .= '_' . $cnt;
                    $itemValue->value = $value[$itemValue->fieldname][$index] ?? '';
                    $input_ = $itemValue->getInput();
                    
                    if ($itemValue->type === 'Calendar'){
                        $itemValue->class = ($field->class) ? $field->class . ' type-calendar' : 'type-calendar';
                    }
                    
                    if ($itemValue->type === 'Calendar'){
                        if ($index == 0){
                            $input_ = str_replace(array($itemValue->name), array($itemValue->name.'['.$index.']'), $input_);
                        }else{
                            $input_ = str_replace(array($itemValue->name, $itemValue->id), array($itemValue->name.'['.$index.']', $itemValue->id.'_'.$index), $input_);
                            JHtml::_('calendar', $itemValue->value, $itemValue->name.'['.$index.']', $itemValue->id.'_'.$index);
                        }
                    }else{
                        $input_ = str_replace(array($itemValue->name, $itemValue->id), array($itemValue->name.'['.$index.']', $itemValue->id.'_'.$index), $input_);
                    }
                    ?>
                    <div>
                    <?php echo $itemValue->getLabel()?>
                    <?php echo $input_ ?>
                    </div>
                <?php endforeach;?>
                </div>
            <?php endforeach;?>
              <div class="action-wrap">
                  <span class="btn action btn-clone" data-action="clone_row" title="<?php echo JText::_('JTOOLBAR_DUPLICATE'); ?>">
                      <i class="icon-plus" title="<?php echo JText::_('JTOOLBAR_DUPLICATE'); ?>"></i>
                  </span>
                  <span class="btn action btn-delete" data-action="delete_row" title="<?php echo JText::_('JTOOLBAR_REMOVE'); ?>">
                      <i class="icon-minus" title="<?php echo JText::_('JTOOLBAR_REMOVE'); ?>"></i>
                  </span>
              </div>
          </div>
				<?php $cnt++ ?>
			<?php endforeach; ?>
    </div>
</div>
    <script type="text/javascript">
        jQuery('.<?php echo $id ?>').jalist();
    </script>
</div>