<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Common class.
 *
 * @extends CI_Model
 */
class Steve extends CI_Model
{
    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        if (!empty($_SESSION['user']->timezone)) {
            date_default_timezone_set('Asia/Kuala_Lumpur');
        }
    }

    public function days($id = null)
    {
        $days = ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"];
        if (isset($id)) {
            return $days[$id];
        } else {
            return $days;
        }
    }

    public function states()
    {
        $resp = $this->API->GET("admin/states/listall");
        if ($resp->state && $resp->results) {
            return $resp->results;
        }
    }

    public function get_configs()
    {
        $resp = $this->API->GET("admin/configs");

        if ($resp->state && $resp->results) {
            $configs = [];
            foreach ($resp->results as $r) {
                $configs[$r->name] = $r;
            }
            return $configs;
        }
    }

    public function worker_attendance()
    {
        return [
            (object) ["id" => "P", "name" => "Present"],
            (object) ["id" => "PL", "name" => "Paid leave"],
            (object) ["id" => "RD", "name" => "Rest day (paid)"],
            (object) ["id" => "ML", "name" => "Medical leave"],
            (object) ["id" => "UL", "name" => "Unpaid / annual Leave"],
            (object) ["id" => "XL", "name" => "Exceptional paid leave"],
        ];
    }

    public function branch_exchange_rate_multipliers($id)
    {
        $result = $this->db->get_where("branch_exchange_rates", ["branch_id" => $id])->result();
        $currencies = [];
        if ($result) {
            foreach ($result as $r) {
                $currencies[$r->currency] = $r->multiplier;
            }
        }
        return $currencies;
    }

    public function currency_convert($rates, $currency, $price, $to_currency = "MYR", $exchange_rate)
    {
        if ($to_currency == $currency) {
            return $price;
        }
        return $price / $rates->{$currency} * $exchange_rate;
    }

    public function currency_convert_rate($rates, $currency, $to_currency = "MYR", $exchange_rate)
    {
        if ($to_currency == $currency) {
            return 1;
        }

        return 1 / $rates->{$currency} * $exchange_rate;
    }

    public function latest_exchange_rates()
    {
        $result = $this->db->limit(1, 0)->order_by("timestamp", "desc")->get_where("exchange_rates", [])->result();
        if ($result) {
            return $result[0];
        }
    }

    public function get_log($category, $id)
    {
        if ($id) {
            $resp = $this->API->GET("logs/" . $category . "/" . $id . "/50");
            if ($resp->state && $resp->results) {
                return $resp->results;
            }
        }
    }

    public function excel_date($str)
    {
        $date = new DateTime($str);

        $date->setTimezone(new DateTimeZone('Asia/Calcutta'));

        return $date->format("Y-m-d H:i:s");
    }

    public function active_toggle($table, $column)
    {
        if ($table == 'companies') {
            $this->db->set("status", intval($this->input->post('active')) == 0 ? 1 : 0);
        } else {
            $this->db->set("active", intval($this->input->post('active')));
        }
        $this->db->where($column, intval($this->input->post('id')));
        if ($this->db->update($table)) {
            $this->logs->add($table, $this->input->post('id'), intval($this->input->post('active')) ? "ITEM_ACTIVE" : "ITEM_DISABLED");
            die(json_encode(["state" => 1]));
        } else {
            die(json_encode(["state" => 0]));
        }
    }

    public function parse_branch_template($id, $html, $additions = [])
    {
        $info = $this->db->join("companies", "companies.company_id = branches.company_id", "left")->get_where('branches', ["branch_id" => $id])->result();

        if ($info) {
            foreach ($info[0] as $var => $val) {
                if ($var == "address_country" || $var == "country") {
                    $val = $this->country_name($val);
                }
                $html = preg_replace("/\{\{" . $var . "\}\}/is", $val, $html);
            }
        }
        foreach ($additions as $placeholder => $value) {
            $html = preg_replace("/\{\{" . $placeholder . "\}\}/is", $value, $html);
        }
        $html = preg_replace("/\{\{month\}\}/is", date("m"), $html);
        $html = preg_replace("/\{\{date\}\}/is", date("d"), $html);
        $html = preg_replace("/\{\{year\}\}/is", date("y"), $html);

        $html = preg_replace("/\{\{user_code\}\}/is", trim($_SESSION['user']->user_code), $html);
        return $html;
    }

    public function random_str(
        $length,
        $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ) {
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        if ($max < 1) {
            throw new Exception('$keyspace must be at least two characters long');
        }
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
    }

        public function datatables_mysql($db, $all_searches = [], $conditions = [], $joins = [], $select = '', $hasGroup = null)
    {
        if ($_POST['search']['value'] && count($all_searches)) {
            $searches = $all_searches;
            $this->db->group_start();
            $this->db->like($all_searches[0], $_POST['search']['value']);
            array_shift($searches);
            foreach ($searches as $search) {
                if ($search) {
                    $this->db->or_like($search, $_POST['search']['value']);
                }
            }
            $this->db->group_end();
        }

        if ($conditions) {
            foreach ($conditions as $condition) {
                if (is_array($condition[0])) {
                    $this->db->group_start();
                    foreach ($condition[0] as $sub_condition) {
                        $this->db->or_where($sub_condition, $condition[1]);
                    }
                    $this->db->group_end();
                } else if (is_array($condition[1])) {
                    $this->db->where_in($condition[0], $condition[1]);
                } else if (is_array($condition)) {
                    $this->db->where($condition[0], $condition[1]);
                } else {
                    $this->db->where($condition);
                }
            }
        }

        // Adding the joins
        if ($joins) {
            foreach ($joins as $join) {
                $this->db->join($join[0], $join[1], ($join[2] ? $join[2] : "left"));
            }
        }

        $this->db->from($db);

        $filtered_results = $this->db->count_all_results();

        $this->db->reset_query();

        if ($select) {
            $this->db->select($select);
        }

        if ($conditions) {
            foreach ($conditions as $condition) {
                if (is_array($condition[0])) {
                    $this->db->group_start();
                    foreach ($condition[0] as $sub_condition) {
                        $this->db->or_where($sub_condition, $condition[1]);
                    }
                    $this->db->group_end();
                } else if (is_array($condition[1])) {
                    $this->db->where_in($condition[0], $condition[1]);
                } else if (is_array($condition)) {
                    $this->db->where($condition[0], $condition[1]);
                } else {
                    $this->db->where($condition);
                }
            }
        }

        if ($_POST['search']['value'] && count($all_searches)) {
            $searches = $all_searches;
            $this->db->group_start();
            $this->db->like($all_searches[0], $_POST['search']['value']);
            array_shift($searches);
            foreach ($searches as $search) {
                if ($search) {
                    $this->db->or_like($search, $_POST['search']['value']);
                }
            }
            $this->db->group_end();
        }

        if ($hasGroup) {
            $this->db->group_by($hasGroup);
        }

        $this->db->order_by($_POST['columns'][$_POST['order'][0]['column']]['data'], $_POST['order'][0]['dir']);

        if ($joins) {
            foreach ($joins as $join) {
                $this->db->join($join[0], $join[1], "left");
            }
        }

        $this->db->limit($_POST['length'], $_POST['start']);

        $query = $this->db->get($db);

        $data = [];

        foreach ($query->result() as $row) {
            if (isset($row->password)) {
                unset($row->password);
            }
            $data[] = $row;
        }

        return json_encode([
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->db->count_all($db),
            "recordsFiltered" => $filtered_results,
            "data" => $data
        ]);
    }




    public function format_branch_placeholders()
    {
        $placeholders = ["COMPANY_CODE", "BRANCH_CODE", "COMPANY_NAME", "BRANCH_NAME", "BRANCH_REGISTRATION", "ADDRESS_LINE_1", "ADDRESS_LINE_2", "ADDRESS_ZIP", "ADDRESS_CITY", "ADDRESS_STATE", "ADDRESS_COUNTRY", "TELEPHONE", "FAX", "YEAR", "MONTH", "DATE", "POL", "POFD", "USER_CODE"];

        $html = '';

        foreach ($placeholders as $p) {
            $html .= '<button type="button" class="btn copy_clipboard btn-sm" data-clipboard-text="{{' . $p . '}}">{{' . $p . '}}</button>';
        }
        return $html;
    }

    public function worker_employment_types()
    {
        return ['permanent', 'outsourced', 'contract'];
    }

    public function outsource_company_name_list()
    {
        return $this->db->select("outsource_company_name")->order_by("outsource_company_name", "asc")->from("workers")->where("active", 1)->where("outsource_company_name !=", '')->get()->result_object();
    }

    public function fault_lists()
    {
        return $this->db->from("fault_lists")->where("active", 1)->get()->result_object();
    }

    public function worker_employment_type_leaves($type = '', $leave_Type, $leave_override = 0, $medical_leave_override = 0)
    {
        if ($leave_override > 0 || $medical_leave_override > 0) {
            log_message("error", $leave_override);
            return $leave_override;
            return $medical_leave_override;
        } else {
            $leaves = ['permanent' => ["annual" => 0, "medical" => 0], 'outsourced' => ["annual" => 0, "medical" => 0], 'contract' => ["annual" => 12, "medical" => 14]];
            if ($type) {
                return $leaves[$type][$leave_Type];
            } else {
                return $leaves;
            }
        }
    }

    public function ftrim($str)
    {
        return trim(str_replace('&nbsp;', " ", preg_replace('~\x{00a0}~siu', ' ', $str)));
    }
    public function form_group_label_input($type, $name, $placeholder, $css = '', $required = 0, $value = '', $maxlength = '', $readonly = 0, $options = '', $nolabel = 0, $decimals = 1)
    {
        return '<div class="form-group' . ($css ? ' ' . $css : '') . '">' . ($placeholder && !$nolabel ? '<label for="form_' . $name .  '">' . $placeholder . ($required ? ' <sup>REQUIRED</sup>' : '') . '</label>' : '') . '<input type="' . $type . '" name="' . $name . '" class="form-control" ' . ($decimals ? 'step="0.00001"' : 'step="1"') . ' id="form_' . $name . '" min="0" ' . $options . ' placeholder="' . $placeholder . '" value="' . $value . '" ' . ($readonly ? 'readonly ' : '') . ($required ? 'required' : '') . ' autocomplete="off" ' . ($maxlength ? 'maxlength="' . $maxlength . '"' : '') . ' /></div>';
    }

    public function form_group_label_input_group($type, $name, $placeholder, $css = '', $input_css = '', $required = 0, $value = '', $maxlength = '', $append = "", $readonly = 0, $decimals = 0)
    {
        return '<div class="input-group' . ($css ? ' ' . $css : '') . '"><div class="input-group-prepend">
        <span class="input-group-text btn-primary disabled"><small>' . $placeholder . '</small></span></div>
        <input type="' . $type . '" id="' . $name . '" class="form-control' . ($input_css ? ' ' . $input_css : '') . '" ' . ($decimals ? 'step="0.01"' : 'step="1"') . ' autocomplete="off" placeholder="' . strip_tags($placeholder) . ($required ? ' *' : '') . '" data-title_text="' . strip_tags($placeholder) . '" value="' . $value . '" name="' . $name . '" ' . ($required ? 'required ' : '') . ($readonly == 1 ? 'readonly ' : ($readonly == 2 ? 'disabled ' : '')) . ($maxlength ? 'maxlength="' . $maxlength . '"' : '') . ' />' . ($append ? '<div class="input-group-append"><span class="input-group-text btn-primary disabled"><i class="fa fa-' . $append . '"></i></span></div>' : '') . '</div>';
    }

    public function form_group_label_input_group_append($type, $name, $placeholder, $css = '', $input_css = '', $required = 0, $value = '', $maxlength = '', $append = "", $readonly = 0)
    {
        return '<div class="input-group' . ($css ? ' ' . $css : '') . ' tip" title="' . $placeholder . '">
        <input type="' . $type . '" id="' . $name . '" class="form-control' . ($input_css ? ' ' . $input_css : '') . '" step="0.0001" autocomplete="off" placeholder="' . $placeholder . ($required ? ' *' : '') . '" value="' . $value . '" name="' . $name . '" ' . ($required ? 'required ' : '') . ($readonly ? 'readonly ' : '') . ($maxlength ? 'maxlength="' . $maxlength . '"' : '') . ' />' . ($append ? '<div class="input-group-append"><span class="input-group-text btn-primary disabled"><i class="fa fa-' . $append . '"></i></span></div>' : '') . '</div>';
    }

    public function form_group_label_textarea($name, $placeholder, $css = '', $required = 0, $value = '', $note = '', $readonly = 0)
    {
        return '<div class="form-group' . ($css ? ' ' . $css : '') . '">
		' . ($placeholder ? '<label for="form_' . $name . '">' . $placeholder . ($required ? ' <sup>REQUIRED</sup>' : '') . '</label>' : '') . '<textarea name="' . $name . '" class="form-control form-control-sm" id="form_' . $name . '" rows="5" placeholder="' . strip_tags($placeholder) . '" ' . ($readonly == 1 ? 'readonly ' : ($readonly == 2 ? 'disabled ' : '')) . ($required ? 'required' : '') . '>' . $value . '</textarea>' . ($note ? '<small class="text-info">' . $note . '</small>' : '') . '</div>';
    }

    public function partition_array(array $list, $p)
    {
        if ($p == 0) {
            return [$list];
        }
        $listlen = count($list);
        $partlen = floor($listlen / $p);
        $partrem = $listlen % $p;
        $partition = array();
        $mark = 0;
        for ($px = 0; $px < $p; $px++) {
            $incr = ($px < $partrem) ? $partlen + 1 : $partlen;
            $partition[$px] = array_slice($list, $mark, $incr);
            $mark += $incr;
        }
        return $partition;
    }

    public function fa_check_times($value)
    {
        if ($value == 1 || $value == "1") {
            return '<i class="fa fa-check text-success"></i>';
        } else {
            return '<i class="fa fa-times"></i>';
        }
    }

    public function form_group_label_checkbox($name, $placeholder, $css = '', $required = 0, $value = '', $checked = 0)
    {
        return '<div class="form-check' . ($css ? ' ' . $css : '') . '">
		<input type="checkbox" name="' . $name . '" class="form-check-input" id="form_' . $name . '" placeholder="' . $placeholder . '" value="' . $value . '" ' . ($required ? 'required' : '') . ($checked ? ' checked' : '') . ' /><label class="form-check-label" for="form_' . $name . '">' . $placeholder . '</label>
		</div>';
    }

    public function form_group_label_radio($name, $placeholder, $css = '', $required = 0, $value = '', $checked = 0)
    {
        return '<div class="form-check' . ($css ? ' ' . $css : '') . '">
		<input type="radio" name="' . $name . '" class="form-check-input" id="form_' . $name . '" placeholder="' . $placeholder . '" value="' . $value . '" ' . ($required ? 'required' : '') . ($checked ? ' checked' : '') . ' /><label class="form-check-label" for="form_' . $name . '">' . $placeholder . '</label>
		</div>';
    }

    public function form_group_input_suffix($name, $placeholder = '', $css = '', $currency = "$", $required = 0, $value = '')
    {
        return '<div class="input-group' . ($css ? ' ' . $css : '') . '">
        <input type="number" max="10000000" step="0.0001" id="form_' . $name . '" name="' . $name . '" class="form-control" aria-label="Amount" placeholder="' . $placeholder . '" value="' . $value . '"' . ($required ? ' required' : '') . '>
        <div class="input-group-append">
          <span class="input-group-text">' . $currency . '</span>
        </div>
      </div>';
    }

    public function form_group_input_suffix_new($id, $name, $placeholder = '', $css = '', $currency = "$", $required = 0, $value = '', $className, $readonly = false)
    {
        if ($readonly == true) {
            $input = '<input readonly type="number" max="10000000" step="0.0001" id="form_' . $id . '" name="' . $name . '" class="form-control' . ' ' . $className . '" aria-label="Amount" placeholder="' . $placeholder . '" value="' . $value . '"' . ($required ? ' required' : '') . '>';
        } else {
            $input = '<input type="number" max="10000000" step="0.0001" id="form_' . $id . '" name="' . $id . '" class="form-control' . ' ' . $className . '" aria-label="Amount" placeholder="' . $placeholder . '" value="' . $value . '"' . ($required ? ' required' : '') . '>';
        }
        return '<div class="input-group' . ($css ? ' ' . $css : '') . '">' . $input . '
                    <div class="input-group-append">
                        <span class="input-group-text">' . $currency . '</span>
                    </div>
                </div>';
    }

    public function make_list($items)
    {
        $count = count($items);

        if ($count === 0) {
            return '';
        }

        if ($count === 1) {
            return $items[0];
        }

        return implode(', ', array_slice($items, 0, -1)) . ' & ' . end($items);
    }

    public function branch_charges($branch_id)
    {
        return $this->db->order_by("charge_name", "asc")->join("charges", "branch_charges.charge_id = charges.charge_id", "left")->get_where("branch_charges", ["branch_id" => $branch_id, "charges.active" => 1, "deleted" => 0])->result();
    }

    public function invoice_pdf_link($id)
    {
        $invoice = $this->db->get_where("invoices", ["invoice_id" => $id])->result();
        if ($invoice) {
            return '<a href="' . site_url("storage/" . $invoice[0]->filename) . '" title="View invoice" class="tip" target="_blank"><i class="fa fa-file-pdf"></i> ' . $invoice[0]->invoice_number . '</a>';
        }
    }

    public function surcharges()
    {
        return $this->db->order_by("charge_name", "asc")->get_where("charges", ["charge_group" => "surcharge", "movement" => "export", "active" => 1])->result();
    }
    public function form_group_label_select($name, $placeholder, $values, $option_id = '', $option_val = '', $css = '', $val = '', $required = 0, $disabled = 0)
    {
        $html = '<div class="form-group' . ($css ? ' ' . $css : '') . '">' . ($placeholder ? '<label for="form_' . $name . '">' . $placeholder . ($required ? ' <sup>REQUIRED</sup>' : '') . '</label>' : '') . '<select name="' . $name . '" class="form-control" data-initial="' . $val . '" id="form_' . $name . '"' . ($required ? ' required' : '') . ($disabled ? ' disabled' : '') . '>';
        if (!$required) {
            $html .= '<option value="">-- ' . ($placeholder ? $placeholder : "Select one") . ' -- </option>';
        }
        if (isset($values) && $values && count($values)) {
            foreach ($values as $value) {
                if ($option_id) {
                    $html .= '<option value="' . $value->{$option_id} . '" ' . ($value->{$option_id} == $val ? 'selected' : '') . '>' . $value->{$option_val} . '</option>';
                } else {
                    $html .= '<option value="' . $value . '" ' . ($value == $val ? 'selected' : '') . '>' . str_replace("_", " ", $value) . '</option>';
                }
            }
        } else {
            $html .= '<option value="" disabled selected>No data found</option>';
        }
        $html .= '</select></div>';

        return $html;
    }
    public function form_group_label_select_placeholder($name, $placeholder, $values, $option_id = '', $option_val = '', $css = '', $val = '', $required = 0, $disabled = 0)
    {
        $html = '<div class="form-group' . ($css ? ' ' . $css : '') . '">' . ($placeholder ? '<label for="form_' . $name . '">' . $placeholder . ($required ? ' <sup>REQUIRED</sup>' : '') . '</label>' : '') . '<select name="' . $name . '" class="form-control" data-initial="' . $val . '" id="form_' . $name . '"' . ($required ? ' required' : '') . ($disabled ? ' disabled' : '') . '>';
        //if (!$required) {
        $html .= '<option value="">-- ' . ($placeholder ? $placeholder : "Select one") . ' -- </option>';
        //} 
        if (isset($values) && $values && count($values)) {
            foreach ($values as $value) {
                $displaytext = "";
                $display_columns = explode(",",  $option_val);
                log_message("error", json_encode($display_columns));
                foreach ($display_columns as $column) {
                    log_message("error", json_encode($value));
                    log_message("error", $column);

                    if ($displaytext == "")
                        $displaytext = $value->{$column};
                    else
                        $displaytext = $displaytext . " - " . $value->{$column};
                }
                if ($option_id) {
                    $html .= '<option value="' . $value->{$option_id} . '" ' . ($value->{$option_id} == $val ? 'selected' : '') . '>' . $displaytext . '</option>';
                } else {
                    $html .= '<option value="' . $value . '" ' . ($value == $val ? 'selected' : '') . '>' . str_replace("_", " ", ucfirst($value)) . '</option>';
                }
            }
        } else {
            $html .= '<option value="" disabled selected>No data found</option>';
        }
        $html .= '</select></div>';

        return $html;
    }

    public function make_address($a, $br = "<br />")
    {

        return ($a->address_line_1 ? $a->address_line_1 . $br : '') . ($a->address_line_2 ? $a->address_line_2 . $br : '') . ($a->address_zip ? $a->address_zip . " " : '') . ($a->address_city ? $a->address_city . " " : '') . ($a->address_state ? $a->address_state . " " : '') . ($a->address_country ? $this->country_name($a->address_country) : '') . ($a->country_code ? $this->country_name($a->country_code) : '') . ($a->telephone ? $br . "TEL: " . $a->telephone : "") . ($a->fax ? $br . "FAX: " . $a->fax : "");
    }

    public function form_group_label_select_group($name, $placeholder, $css = '', $select_css = '', $required = 0, $values = [], $val = '', $option_id = '', $option_val = '', $append = "")
    {
        $html = '<div class="input-group' . ($css ? ' ' . $css : '') . '">' . ($placeholder ? '<div class="input-group-prepend">
        <span class="input-group-text btn-primary disabled"><small>' . $placeholder . '</small></span></div>' : '') . '<select name="' . $name . '" class="form-control" id="form_' . $name . '"' . ($required ? ' required' : '') . '>';

        if (!$required) {
            $html .= '<option value="">-- ' . ($placeholder ? $placeholder : "Select one") . ' -- </option>';
        }

        if (count($values)) {
            foreach ($values as $value) {
                if ($option_id) {
                    $html .= '<option value="' . $value->{$option_id} . '" ' . ($value->{$option_id} == $val ? 'selected' : '') . '>' . $value->{$option_val} . '</option>';
                } else {
                    $html .= '<option value="' . $value . '" ' . ($value == $val ? 'selected' : '') . '>' . $value . '</option>';
                }
            }
        } else {
            $html .= '<option value="" disabled selected>No data found</option>';
        }
        $html .= '</select>' . ($append ? '<div class="input-group-append"><span class="input-group-text btn-primary disabled"><i class="fa fa-' . $append . '"></i></span></div>' : '') . '</div>';

        return $html;
    }

    public function vessel_types()
    {
        return ["CONTAINER", "GENERAL CARGO", "BULK", "PASSENGER", "CAR CARRIER", "ORE", "OTHER", "RORO/REEFER CARRIER", "CHEMICAL", "LIQUIFIED PETROLEUM GAS TANKER", "MULTI-PURPOSE(SEMI CONT.)", "TANKER (CRUDE,FUEL,DIESEL,LUB)", "TUG", "BULKER", "LIQUIFIED NATURAL GAS TANKER", "LIQUIFIED PETROLEUM  GAS TANKER", "GENERAL CAGRO", "TANKER (EDIBLE OIL, SEWAGE)"];
    }

    public function countries($show_all = 0)
    {
        if ($show_all) {
            return $this->db->get_where("countries", [])->result();
        } else {
            return $this->db->get_where("countries", ["active" => 1])->result();
        }
    }

    public function vessels()
    {
        return $this->db->order_by("vessel_name", "asc")->get_where("vessels", ["active" => 1])->result();
    }

    public function resource_types()
    {
        return $this->db->order_by("resource_type_name", "asc")->get_where("resource_types", ["active" => 1])->result();
    }

    public function service_types()
    {
        return $this->db->order_by("service_type_name", "asc")->get_where("service_types", [])->result();
    }

    public function branch_office_lists()
    {
        return $this->db->order_by("branch_name", "asc")->get_where("branch_office", ["active" => 1])->result();
    }

    public function licence_type_lists()
    {
        return $this->db->order_by("licence_name", "asc")->get_where("licence_type", ["active" => 1])->result();
    }

    public function resource_type($id)
    {
        $resource = $this->db->get_where("resource_types", ["active" => 1, "resource_type_id" => $id])->result();
        if ($resource) {
            return $resource[0];
        }
    }

    public function resource_type_colour($id)
    {
        $resource = $this->resource_type($id);
        if ($resource) {
            return $resource->resource_type_colour;
        }
    }

    public function reset_worker_order()
    {
        $workers = $this->db->order_by("worker_order")->get_where("workers", ["active" => 1])->result();

        $i = 1;
        foreach ($workers as $worker) {
            $this->db->reset_query();
            $this->db->set("worker_order", $i++);
            $this->db->where("worker_id", $worker->worker_id);
            $this->db->update("workers");
        }
    }

    public function push_worker_order_down($worker_id)
    {
        $last_order = $this->db->select("max(worker_order) as last_order")->get_where("workers", ["active" => 1])->result();

        $this->db->set("worker_order", $last_order[0]->last_order + 1);
        $this->db->where("worker_id", $worker_id);
        return $this->db->update("workers");
    }

    public function push_worker_order_up($worker_id)
    {
        $last_order = $this->db->select("min(worker_order) as last_order")->get_where("workers", ["active" => 1])->result();

        $this->db->set("worker_order", $last_order[0]->last_order - 1);
        $this->db->where("worker_id", $worker_id);
        return $this->db->update("workers");
    }

    public function worker_absent($worker_id, $date)
    {
        $check = $this->db->get_where("worker_availability", ['availability_date' => $date, 'worker_id' => $worker_id, 'deleted' => 0])->result();
        if (!$check || $check[0]->worker_attendance == "P") {
            return false;
        } else {
            return true;
        }
        //        $worker->worker_attendance && $worker->worker_attendance != 'P'
    }

    public function workers()
    {
        return $this->db->order_by("worker_name", "asc")->get_where("workers", ["active" => 1])->result();
    }

    public function equipments()
    {
        return $this->db->order_by("equipment_name", "asc")->get_where("equipments", ["active" => 1])->result();
    }

    public function equipment_types()
    {
        return $this->db->order_by("equipment_type_name", "asc")->get_where("equipment_types", ["active" => 1])->result();
    }

    public function asset_types()
    {
        return $this->db->order_by("name", "asc")->get_where("asset_types", ["active" => 1])->result();
    }

    public function preventive_dashboard()
    {
        $asset_types = $this->db->select('asset_id, name as asset_type, asset_picture')
            ->from('asset_types')
            ->where('active', 1)
            ->order_by('name', 'asc')
            ->get()
            ->result();

        $data = [];

        foreach ($asset_types as $type) {
            $asset_id = $type->asset_id;
            $asset_name = $type->asset_type;
            $asset_picture = $type->asset_picture;

            $status_counts = [
                'complete'    => 0,
                'Pending'     => 0,
                'Maintenance' => 0
            ];

            $equipments = $this->db->select('
            equipments_asset.*,
            latest_maintenance.update_date AS latest_maintenance_date,
            latest_task.remarks AS latest_remarks,
            GROUP_CONCAT(DISTINCT equipment_maintenance_asset.update_date ORDER BY equipment_maintenance_asset.update_date ASC) AS maintenance_history,
            next_maintenance_date.maintenance_date AS next_maintenance_date
        ')
                ->from('equipments_asset')
                ->join('next_maintenance_date', 'next_maintenance_date.equipment_id = equipments_asset.equipment_id', 'left')
                ->join('equipment_maintenance_asset', 'equipment_maintenance_asset.equipment_id = equipments_asset.equipment_id', 'left')
                ->join('(SELECT ema.* FROM equipment_maintenance_asset ema JOIN (SELECT equipment_id, MAX(created_at) AS max_created_at FROM equipment_maintenance_asset GROUP BY equipment_id) latest_ema ON ema.equipment_id = latest_ema.equipment_id AND ema.created_at = latest_ema.max_created_at WHERE ema.maintenance_type_id = "preventive") AS latest_maintenance', 'latest_maintenance.equipment_id = equipments_asset.equipment_id', 'left')
                ->join('(SELECT mtd.* FROM maintenance_task_done mtd JOIN (SELECT equipment_maintenance_id, MAX(created_at) AS max_created_at FROM maintenance_task_done GROUP BY equipment_maintenance_id) latest_mtd ON mtd.equipment_maintenance_id = latest_mtd.equipment_maintenance_id AND mtd.created_at = latest_mtd.max_created_at) AS latest_task', 'latest_task.equipment_maintenance_id = latest_maintenance.equipment_id', 'left')
                ->where('equipments_asset.maintenance_date IS NOT NULL', null, false)
                ->where('equipments_asset.frequency_year IS NOT NULL', null, false)
                ->where('equipments_asset.maintenance_reminder_day IS NOT NULL', null, false)
                ->where('equipments_asset.equipment_type', $asset_id)
                ->group_by('equipments_asset.equipment_id')
                ->get()
                ->result();

            foreach ($equipments as $d) {
                if (empty($d->next_maintenance_date)) {
                    continue;
                }

                try {
                    $next_maintenance_date = new DateTime($d->next_maintenance_date);
                } catch (Exception $e) {
                    continue;
                }

                $currentDate     = new DateTime();
                $reminder_days   = (int) $d->maintenance_reminder_day;
                $reminder_date   = (clone $next_maintenance_date)->modify("-$reminder_days days");
                $status          = null;
                $latest_same_as_next = false;

                if (!empty($d->latest_maintenance_date)) {
                    try {
                        $latest_dt = new DateTime($d->latest_maintenance_date);
                        if ($latest_dt->format("Y-m-d") === $next_maintenance_date->format("Y-m-d")) {
                            $latest_same_as_next = true;
                        }

                        if ($latest_dt >= $next_maintenance_date) {
                            $status = "complete";
                        } elseif ($next_maintenance_date < $currentDate) {
                            $status = "Pending";
                        } elseif ($currentDate >= $reminder_date && $currentDate < $next_maintenance_date) {
                            $status = "Maintenance";
                        }
                    } catch (Exception $e) {
                        $status = "Pending";
                    }
                } else {
                    $status = ($next_maintenance_date >= $currentDate) ? "complete" : "Pending";
                }

                // Count history entries as completed
                if (!empty($d->maintenance_history)) {
                    $history_dates = array_filter(array_map('trim', explode(',', $d->maintenance_history)));
                    $status_counts['complete'] += count($history_dates);
                }

                // Apply duplicate skip logic
                if ($status === 'complete' && $latest_same_as_next) {
                    continue;
                }

                // Count upcoming/main status
                if ($status !== null) {
                    $status_counts[$status]++;
                }
            }

            $total = array_sum($status_counts);

            $data[] = [
                'asset_type'     => $asset_name,
                'asset_id'       => $asset_id,
                'total'          => $total,
                'statuses'       => $status_counts,
                'asset_picture'  => $asset_picture,
                'equipment_ids'  => array_column($equipments, 'equipment_id'),
            ];
        }

        return $data;
    }






    public function assets_type_dashboard()
    {
        $asset_types = $this->db->select('asset_types.asset_id, asset_types.name as asset_type, asset_types.asset_picture')
            ->from('asset_types')
            ->where('asset_types.active', 1)
            ->order_by('asset_types.name', 'asc')
            ->get()
            ->result();

        $data = [];

        foreach ($asset_types as $type) {
            $asset_id = $type->asset_id;
            $asset_name = $type->asset_type;
            $asset_picture = $type->asset_picture;

            $assets = $this->db->select('equipments_asset.equipment_id, equipments_asset.equipment_picture, asset_status.name as status_name')
                ->from('equipments_asset')
                ->join('asset_status', 'asset_status.name = equipments_asset.equipment_status', 'left')
                ->where('equipments_asset.equipment_type', $asset_id)
                ->get()
                ->result();

            $status_counts = [];
            $total = 0;
            $representative_picture = null;
            $representative_equipment_id = null;

            foreach ($assets as $a) {
                $status = strtoupper(trim($a->status_name ?? 'UNKNOWN'));

                if (!isset($status_counts[$status])) {
                    $status_counts[$status] = 0;
                }
                $status_counts[$status]++;
                $total++;

                // Capture the first available picture and its equipment_id as representative
                if (!$representative_picture && !empty($a->equipment_picture)) {
                    $representative_picture = $a->equipment_picture;
                    $representative_equipment_id = $a->equipment_id;
                }
            }

            $data[] = [
                'asset_type' => $asset_name,
                'asset_id' => $asset_id,
                'total' => $total,
                'statuses' => $status_counts,
                'equipment_picture' => $representative_picture,
                'asset_picture' => $asset_picture,
                'equipment_id' => $representative_equipment_id // Added equipment_id
            ];
        }

        return $data;
    }

    public function item_types()
    {
        return $this->db->order_by("name", "asc")->get_where("item_types")->result();
    }


    public function items_type_dashboard()
    {
        $item_types = $this->db->select('item_types.id as item_type_id, item_types.name as item_type_name, item_picture as item_picture')
            ->from('item_types')
            ->order_by('item_types.name', 'asc')
            ->get()
            ->result();

        $data = [];

        foreach ($item_types as $type) {
            $type_id = $type->item_type_id;
            $type_name = $type->item_type_name;
            $item_picture = $type->item_picture;

            $items = $this->db->select('add_asset_items.id, is.name as item_status')
                ->from('add_asset_items')
                ->join('item_status AS is', 'add_asset_items.item_status_id = is.id', 'left')
                ->where('add_asset_items.item_type_id', $type_id)
                ->get()
                ->result();

            $status_counts = [];
            $total = 0;
            $representative_item_id = null;

            foreach ($items as $item) {
                $status = strtoupper(trim($item->item_status ?? 'UNKNOWN'));

                if (!isset($status_counts[$status])) {
                    $status_counts[$status] = 0;
                }
                $status_counts[$status]++;
                $total++;

                if (!$representative_item_id) {
                    $representative_item_id = $item->id;
                }
            }

            $data[] = [
                'item_type' => $type_name,
                'item_type_id' => $type_id,
                'total' => $total,
                'statuses' => $status_counts,
                'item_picture' => $item_picture,
                'item_id' => $representative_item_id
            ];
        }

        return $data;
    }






    public function insurance_companies()
    {
        return $this->db->order_by("name", "asc")->get_where("insurance_companies", ["active" => 1])->result();
    }

    public function ownerships()
    {
        return ['Company', 'Outsourced'];
    }

    public function incident_requests()
    {
        return $this->db->order_by("incident_request_id", "asc")->get_where("incident_requests", [])->result();
    }
    public function vessel_visit_id()
    {
        $vistids_data = $this->db->select('vessel_visits.vessel_visit_id, vessels.vessel_name, vessel_visits.visit_eta')
            ->from("vessel_visits")
            ->join("vessels", "vessels.vessel_id = vessel_visits.vessel_id")
            ->order_by('vessel_visits.visit_eta', 'desc')
            ->group_by('vessel_visits.vessel_visit_id')
            ->where("vessel_visits.deleted", 0)
            ->get()->result();
        return $vistids_data;
    }
    public function incident_types()
    {
        return $this->db->order_by("incident_type", "asc")->get_where("incident_types", ["active" => 1])->result();
    }
    public function masters_companies()
    {
        return $this->db->order_by("company_name", "asc")->get_where("masters_companies", ["active" => 1])->result();
    }

    public function worker_locations()
    {
        return $this->db->order_by("worker_location_name", "asc")->get_where("worker_locations", ["active" => 1])->result();
    }

    public function gear_types()
    {
        return $this->db->order_by("gear_type_name", "asc")->get_where("gear_types", ["active" => 1])->result();
    }

    public function operation_types()
    {
        return $this->db->order_by("operation_type_name", "asc")->get_where("operation_types", ["active" => 1])->result();
    }

    public function commodities($all_packaging = 0)
    {
        return $this->db->order_by("commodity_code", "asc")->get_where("commodities", ["commodities.active" => 1, "commodities.active" => 1])->result();
    }

    public function cargo_types()
    {
        return $this->db->order_by("cargo_type_name", "asc")->get_where("cargo_types", ["active" => 1])->result();
    }

    public function cargo_packagings()
    {
        return $this->db->order_by("cargo_packaging_name", "asc")->get_where("cargo_packagings", ["active" => 1])->result();
    }

    public function shipment_terms()
    {
        return $this->db->get_where("shipment_terms", ["active" => 1])->result();
    }

    public function operators()
    {
        return $this->db->get_where("operators", ["active" => 1])->result();
    }

    public function wastage_types()
    {
        return $this->db->get_where("wastage_types", ["active" => 1])->result();
    }

    public function charge_groups()
    {
        return [
            (object) ["id" => "local", "name" => "Local charge"],
            (object) ["id" => "port", "name" => "Port charge"],
            (object) ["id" => "surcharge", "name" => "Surcharge"],
            (object) ["id" => "commission", "name" => "Commission"],
            (object) ["id" => "admin", "name" => "Administration charge"],
            (object) ["id" => "others", "name" => "Others"],
        ];
    }
    public function rental_duration()
    {
        return ["Day", "Week", "Month"];
    }

    public function equipment_statuses()
    {
        return [];
    }
    public function status()
    {
        return ["In use", "Sold", "Available", "Repair", "Dispose", "Scrap"];
    }
    public function sold()
    {
        return ["Purchased By", "Purchase Price"];
    }

    public function gear_statuses()
    {
        return [
            (object) ["id" => 0, "name" => "In use"],
            (object) ["id" => 1, "name" => "Damaged"],
        ];
    }

    public function company_statuses()
    {
        return [
            (object) ["id" => 0, "name" => "Active"],
            (object) ["id" => 1, "name" => "Suspended"],
            (object) ["id" => 2, "name" => "Terminated"],
        ];
    }

    public function container_inout_types()
    {
        return ["Purchase", "Free Use", "On Hire", "Sublease"];
    }

    public function merchant_name($id)
    {
        if ($id) {
            $info = $this->db->select('merchant_name')->get_where("merchants", ["merchant_id" => $id])->result();
            if ($info) {
                return $info[0]->merchant_name;
            }
        }
    }

    public function operation_type($id)
    {
        if ($id) {
            $info = $this->db->select('operation_type_name')->get_where("operation_types", ["operation_type_id" => $id])->result();
            if ($info) {
                return $info[0]->operation_type_name;
            }
        }
    }

    public function branch_shipping_agent($id)
    {
        $info = $this->db->get_where("branch_shipping_agents", ["branch_shipping_agent_id" => $id])->result();
        if ($info) {
            return ($info[0]->shipping_agent_code ? $info[0]->shipping_agent_code . " - " : '') . $info[0]->shipping_agent_name;
        }
    }

    public function merchant_branch_code($id, $branch_id)
    {
        $info = $this->db->select('merchant_branch_code')->get_where("merchant_branches", ["merchant_id_k" => $id, "branch_id" => $branch_id])->result();
        if ($info) {
            return $info[0]->merchant_branch_code;
        }
    }
    public function depot_code($id)
    {
        $info = $this->db->get_where("depots", ["depot_id" => $id])->result();
        if ($info) {
            return $info[0]->country_code . $info[0]->depot_code;
        }
    }

    public function dg_imo_classes()
    {
        return array(
            (object) ["id" => 0, "name" => "N/A"],
            (object) ["id" => 1, "name" => "Class I"],
            (object) ["id" => 2, "name" => "Class II"],
            (object) ["id" => 3, "name" => "Class III"], /*,
        (object) ["id" => 4.1, "name" => "Class 4.1"],
        (object) ["id" => 4.2, "name" => "Class 4.2"],
        (object) ["id" => 4.3, "name" => "Class 4.3"],
        (object) ["id" => 5.1, "name" => "Class 5.1"],
        (object) ["id" => 5.2, "name" => "Class 5.2"],
        (object) ["id" => 6.1, "name" => "Class 6.1"],
        (object) ["id" => 6.2, "name" => "Class 6.2"],
        (object) ["id" => 7, "name" => "Class 7"],
        (object) ["id" => 8, "name" => "Class 8"],
        (object) ["id" => 9, "name" => "Class 9"]*/
        );
    }

    public function dateDiffInDays($date1, $date2)
    {
        // Calulating the difference in timestamps
        $diff = strtotime($date2) - strtotime($date1);

        // 1 day = 24 hours
        // 24 * 60 * 60 = 86400 seconds
        return abs(round($diff / 86400));
    }
    public function to_date($date = '')
    {
        return date('Y-m-d', strtotime($date ? str_replace('/', '-', $date) : 'now'));
    }

    public function to_datepicker($date, $full_month = 0)
    {
        if ($date) {
            if ($full_month) {
                return date('d-M-Y', strtotime(str_replace('/', '-', $date)));
            } else {
                return date('d/m/Y', strtotime(str_replace('/', '-', $date)));
            }
        }
    }

    public function to_full_format($date = '')
    {
        return date("d F Y", strtotime($date ? str_replace('/', '-', $date) : 'now'));
    }

    public function to_date_time($date, $timezone = 0)
    {
        if ($date) {
            if ($timezone && $_SESSION['user']->timezone) {
                date_default_timezone_set('Asia/Kuala_Lumpur');
                $datetime = new DateTime($date);
                $datetime->setTimezone(new DateTimeZone($_SESSION['user']->timezone));
                return $datetime->format('Y-m-d H:i:s');
            }
            return date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $date)));
        }
    }

    public function to_date_time_p($date)
    {
        if ($date) {
            return date('d/m/Y H:i', strtotime(str_replace('/', '-', $date)));
        }
    }

    public function sea_ports($country_id)
    {
        return $this->db->order_by("ports.starred", "desc")->order_by("port_name", "asc")->select("ports.port_id, CONCAT(ports.port_name, ' (', ports.country_code, ports.port_code, ')') as port_name_desc")->join('countries', 'countries.code = ports.country_code', 'left')->get_where("ports", ["countries.country_id" => intval($country_id), "ports.sea" => 1, "ports.active" => 1])->result();
    }

    public function get_port_code($id, $country = 1)
    {
        $query = $this->db->get_where("ports", ["ports.port_id" => intval($id)])->result();
        if ($query) {
            return ($country ? $query[0]->country_code : '') . $query[0]->port_code;
        }
        return null;
    }

    public function get_port_name($id)
    {
        $query = $this->db->get_where("ports", ["ports.port_id" => intval($id)])->result();
        if ($query) {
            return $query[0]->port_name;
        }
    }

    public function get_port_wharf_name($id)
    {
        $query = $this->db->join("ports", "ports.port_id = port_wharfs.port_id", "left")->get_where("port_wharfs", ["port_wharf_id" => intval($id)])->result();
        if ($query) {
            return $query[0]->wharf_id;
        }
    }

    public function get_port_name_country($id)
    {
        $query = $this->db->join("countries", "countries.code = ports.country_code", "left")->get_where("ports", ["ports.port_id" => intval($id)])->result();
        if ($query) {
            return $query[0]->port_name . ", " . $query[0]->countryname;
        }
    }

    public function vessel_name($id)
    {
        $query = $this->db->get_where("vessels", ["vessel_id" => intval($id)])->result();
        if ($query) {
            return $query[0]->vessel_name;
        }
    }

    public function vessel_hatches($id)
    {
        return $this->db->get_where("vessel_hatches", ["vessel_id" => intval($id), "deleted" => 0])->result();
    }

    public function company_name($id)
    {
        $query = $this->db->get_where("companies", ["company_id" => intval($id)])->result();
        if ($query) {
            return $query[0]->company_name;
        }
    }

    public function company_location_id($id)
    {
        $query = $this->db->get_where("company_addresses", ["company_address_id" => intval($id)])->result();
        if ($query) {
            return $query[0]->location_id;
        }
    }

    public function commodity_code_name($id)
    {
        $query = $this->db->get_where("commodities", ["commodity_id" => intval($id)])->result();
        if ($query) {
            return $query[0]->commodity_code . " - " . $query[0]->description;
        }
    }

    public function country_name($country_code)
    {
        $info = $this->db->get_where('countries', ["code" => $country_code])->result();
        if ($info) {
            return $info[0]->countryname;
        }

        return $country_code;
    }

    public function user_groups()
    {
        return $this->db->where("active", 1)->get("user_groups")->result();
    }

    public function delay_reasons()
    {
        return $this->db->where("active", 1)->get("delay_reasons")->result();
    }

    public function tally_remarks()
    {
        return $this->db->where("active", 1)->get("tally_remarks")->result();
    }

    public function users()
    {
        return $this->db->where("active", 1)->get("users")->result();
    }

    public function companies()
    {
        return $this->db->where("active", 1)->get("companies")->result();
    }

    public function templates($group)
    {
        return $this->db->where("template_group", $group)->get("templates")->result();
    }

    public function fancy_implode($arr)
    {
        if ($arr) {
            array_push($arr, implode(' and ', array_splice($arr, -2)));
            return implode(', ', $arr);
        }
    }

    public function branches($country_code = '')
    {
        if ($country_code) {
            return $this->db->select("companies.company_id, companies.company_code, company_name, company_registration, company_logo, branches.branch_id, branches.branch_code, branch_name, branch_registration, telephone, mobile, person_contact, branches.address_country, default_currency, url, branches.active, CONCAT(branches.branch_code, ' - ', branch_name) as branch_name_code")->where("branches.active", 1)->join('companies', 'companies.company_id = branches.company_id', 'left')->order_by("branches.branch_code", "asc")->get_where("branches", ["address_country" => $country_code])->result();
        }
        return $this->db->select("companies.company_id, companies.company_code, company_name, company_registration, company_logo, branches.branch_id, branches.branch_code, branch_name, branch_registration, telephone, fax, mobile, person_contact, branches.address_country, default_currency, url, branches.active, CONCAT(branches.branch_code, ' - ', branch_name) as branch_name_code")->where("branches.active", 1)->join('companies', 'companies.company_id = branches.company_id', 'left')->order_by("branches.branch_code", "asc")->get("branches")->result();
    }

    public function user_roles()
    {
        return $this->db->get("roles")->result();
    }

    public function worker_groups()
    {
        return $this->db->get_where("worker_groups", ["active" => 1])->result();
    }
    public function trucks()
    {
        return $this->db->order_by("equipment_name", "asc")->get_where("equipments", ["active" => 1])->result();
    }
    public function worker_groups_operations()
    {
        return $this->db->get_where("worker_groups", ["active" => 1, "payroll_start" => NULL])->result();
    }

    public function equipment_groups()
    {
        return $this->db->get_where("equipment_groups", ["active" => 1])->result();
    }
    public function consumables()
    {
        return $this->db->get_where("consumables", ["active" => 1])->result();
    }

    public function manufacturers()
    {
        return $this->db->get_where("manufacturers", ["active" => 1])->result();
    }
    public function bank()
    {
        return $this->db->get_where("banks", ["active" => 1])->result();
    }

    public function rebundling_colours()
    {
        return $this->db->get_where("rebundling_colours", ["active" => 1])->result();
    }

    public function permissions($cat_id = '')
    {
        if ($cat_id) {
            return $this->db->where("perm_cat_id", $cat_id)->get("permissions")->result();
        } else {
            return $this->db->get("permissions")->result();
        }
    }

    public function id_decode($id = 0)
    {
        $data = ($id ? $id : $this->input->get('id'));
        if (base64_encode(base64_decode($data, true)) === $data) {
            return intval(str_replace("STeVe-", "", base64_decode($data)));
        } else {
            return intval($data);
        }
    }

    public function id_encode($id)
    {
        return base64_encode("STeVe-" . $id);
    }

    public function designations()
    {
        return $this->db->where("active", 1)->get("designations")->result();
    }

    public function permission_categories()
    {
        $this->db->from("permission_categories");

        // Older installations do not have a status column on this table.
        if ($this->db->field_exists('status', 'permission_categories')) {
            $this->db->where('status', 1);
        }

        return $this->db->get()->result();
    }

    public function get_role_permissions($id)
    {
        if (is_array($id) && count($id)) {
            $results = $this->db->where_in("role_id", $id)->get_where('role_permissions')->result();
        } else {
            $results = $this->db->get_where('role_permissions', ["role_id" => intval($id)])->result();
        }
        $permissions = [];
        foreach ($results as $r) {
            $permissions[] = $r->perm_id;
        }
        return $permissions;
    }

    public function get_user_roles($id)
    {
        $results = $this->db->get_where('user_role', ["user_id" => intval($id)])->result();
        $roles = [];
        foreach ($results as $r) {
            $roles[] = $r['role_id'];
        }
        die;
        return $roles;
    }

    public function add_export_charges($id, $onetime = 0)
    {
        $booking = $this->db->join('branches', 'branches.branch_id = bookings.branch_id', 'left')->get_where("bookings", ["booking_id" => $id])->result();

        $charges = $this->db->order_by("charge_name", "asc")->join("charges", "branch_charges.charge_id = charges.charge_id", "left")->join("bookings", "bookings.booking_id = " . $id, "left")->join("branches", "branches.branch_id = branch_charges.branch_id", "left")->where("branch_charges.branch_id = bookings.branch_id")->where("branch_charges.shipment_term_1 = bookings.shipment_term_1")->where("branch_charges.shipment_term_2 = bookings.shipment_term_2")->where("branch_charges.container_ownership = bookings.container_ownership")->get_where("branch_charges", ["charges.movement" => "export", "branch_charges.auto_add" => 1, "charges.active" => 1, "branch_charges.deleted" => 0])->result();

        $rates = $this->latest_exchange_rates();

        if ($charges) {

            $containers = $this->db->join("booking_containers", "booking_containers.booking_shipment_id = booking_shipments.booking_shipment_id", "left")->join("containers", "containers.container_id = booking_containers.container_id", "left")->get_where("booking_shipments", ["booking_shipments.booking_id" => $booking[0]->booking_id])->result();

            $typesizes = [];
            foreach ($containers as $c) {
                $typesizes[$c->typesize . "-" . $c->dg]++;
            }

            $first_vessel_info = $this->db->join("ports", "ports.port_id = vessel_visits.port_now", "left")->get_where("vessel_visits", ["vessel_visit_id" => $booking[0]->vessel_1_visit])->result();

            foreach ($charges as $charge) {

                if (!$onetime || !$charge->container_typesize) {

                    if (!$charge->container_typesize) {
                        $quantity = 1;
                    } else if ($typesizes[$charge->container_typesize . "-" . $charge->dg]) {
                        $quantity = $typesizes[$charge->container_typesize . "-" . $charge->dg];
                    } else {
                        continue;
                    }

                    $converted_rate = round($this->currency_convert_rate($rates, $charge->charge_currency, $charge->default_currency, $first_vessel_info[0]->exchange_rate), 2);

                    $this->db->reset_query();
                    $this->db->set("booking_id", $charge->booking_id);
                    $this->db->set("branch_charge_id", $charge->branch_charge_id);
                    $this->db->set("booking_payment_type", $charge->payment_type);
                    if ($charge->payment_type == "Collect") {
                        $this->db->set("collection_branch_id", $charge->destination_branch_id);
                    }
                    $this->db->set("exchange_rate", $converted_rate);
                    $this->db->set("quantity", $quantity);
                    $this->db->set("auto_added", 1);
                    $this->db->insert("booking_charges");
                }
            }
        }
    }

    public function get_config($config_name)
    {
        $this->db->reset_query();
        $value = $this->db->get_where("configs", ["config_name" => $config_name])->result();
        if ($value) {
            return $value[0]->config_value;
        }
    }

    public function set_config($config_name, $config_value)
    {
        $this->db->reset_query();
        $this->db->set("config_value", $config_value);
        $this->db->where("config_name", $config_name);
        return $this->db->update("configs");
    }

    public function add_import_charges($id, $onetime = 0)
    {
        $charges = $this->db->order_by("charge_name", "asc")->join("charges", "branch_charges.charge_id = charges.charge_id", "left")->join("bookings", "bookings.booking_id = " . $id, "left")->join("branches", "branches.branch_id = branch_charges.branch_id", "left")->where("branch_charges.branch_id = bookings.destination_branch_id")->where("branch_charges.shipment_term_1 = bookings.shipment_term_1")->where("branch_charges.shipment_term_2 = bookings.shipment_term_2")->where("branch_charges.container_ownership = bookings.container_ownership")->get_where("branch_charges", ["charges.movement" => "import", "branch_charges.auto_add" => 1, "charges.active" => 1, "branch_charges.deleted" => 0])->result();

        $rates = $this->latest_exchange_rates();

        if ($charges) {

            $containers = $this->db->join("booking_containers", "booking_containers.booking_shipment_id = booking_shipments.booking_shipment_id", "left")->join("containers", "containers.container_id = booking_containers.container_id", "left")->get_where("booking_shipments", ["booking_shipments.booking_id" => $charges[0]->booking_id])->result();

            $typesizes = [];
            foreach ($containers as $c) {
                $typesizes[$c->typesize]++;
            }

            if ($charges[0]->vessel_4_visit) {
                $pofd_visit = $charges[0]->vessel_4_visit;
            } else if ($charges[0]->vessel_3_visit) {
                $pofd_visit = $charges[0]->vessel_3_visit;
            } else if ($charges[0]->vessel_2_visit) {
                $pofd_visit = $charges[0]->vessel_2_visit;
            } else {
                $pofd_visit = $charges[0]->vessel_1_visit;
            }

            $vessel_info = $this->db->join('vessels', 'vessels.vessel_id = vessel_visits.vessel_id', 'left')->join("ports", "ports.port_id = vessel_visits.port_next", "left")->get_where("vessel_visits", ["vessel_visit_id" => $pofd_visit])->result();

            $final_vessel_info = $this->db->order_by("vessel_visits.visit_eta", "asc")->join('vessels', 'vessels.vessel_id = vessel_visits.vessel_id', 'left')->join("ports", "ports.port_id = vessel_visits.port_now", "left")->where("vessel_visits.visit_eta > '" . $vessel_info[0]->visit_eta . "'")->get_where("vessel_visits", ["vessel_visits.port_now" => $vessel_info[0]->port_next, "vessel_visits.vessel_id" => $vessel_info[0]->vessel_id], [1, 0])->result();

            foreach ($charges as $charge) {
                if (!$onetime || $onetime && !$charge->container_typesize) {

                    if (!$charge->container_typesize) {
                        $quantity = 1;
                    } else if ($typesizes[$charge->container_typesize]) {
                        $quantity = $typesizes[$charge->container_typesize];
                    } else {
                        continue;
                    }

                    $converted_rate = round($this->currency_convert_rate($rates, $charge->charge_currency, $charge->default_currency, $final_vessel_info[0]->exchange_rate), 2);

                    $this->db->reset_query();
                    $this->db->set("booking_id", $charge->booking_id);
                    $this->db->set("branch_charge_id", $charge->branch_charge_id);
                    $this->db->set("booking_payment_type", $charge->payment_type);
                    if ($charge->payment_type == "Collect") {
                        $this->db->set("collection_branch_id", $charge->destination_branch_id);
                    }
                    $this->db->set("exchange_rate", $converted_rate);
                    $this->db->set("quantity", $quantity);
                    $this->db->set("auto_added", 1);
                    $this->db->insert("booking_charges");
                }
            }
        }
    }

    public function add_ocean_freight_charges($id)
    {

        $booking = $this->db->join('branches', 'branches.branch_id = bookings.branch_id', 'left')->get_where("bookings", ["booking_id" => $id])->result();

        if ($booking) {

            if ($booking[0]->vessel_4_visit) {
                $pofd_visit = $booking[0]->vessel_4_visit;
            } else if ($booking[0]->vessel_3_visit) {
                $pofd_visit = $booking[0]->vessel_3_visit;
            } else if ($booking[0]->vessel_2_visit) {
                $pofd_visit = $booking[0]->vessel_2_visit;
            } else {
                $pofd_visit = $booking[0]->vessel_1_visit;
            }

            $vessel_info = $this->db->join('vessels', 'vessels.vessel_id = vessel_visits.vessel_id', 'left')->join("ports", "ports.port_id = vessel_visits.port_next", "left")->get_where("vessel_visits", ["vessel_visit_id" => $pofd_visit])->result();

            $first_vessel_info = $this->db->join("ports", "ports.port_id = vessel_visits.port_now", "left")->get_where("vessel_visits", ["vessel_visit_id" => $booking[0]->vessel_1_visit])->result();

            $containers = $this->db->join("quotation_prices", "quotation_prices.quotation_prices_id = booking_shipments.quotation_prices_id", "left")->join("quotation_ports", "quotation_prices.quotation_port_id = quotation_ports.quotation_port_id", "left")->join("booking_containers", "booking_containers.booking_shipment_id = booking_shipments.booking_shipment_id", "left")->join("containers", "containers.container_id = booking_containers.container_id", "left")->get_where("booking_shipments", ["booking_shipments.booking_id" => $id])->result();

            if ($containers) {

                $rates = $this->latest_exchange_rates();

                $sets = [];
                foreach ($containers as $c) {
                    $sets[$c->origin_port_id . "-" . $c->destination_port_id . "-" . $c->price . "-" . $c->typesize . "-" . $c->dg . "-" . $c->container_load . "-" . $c->movement_type][] = $c;
                }

                foreach ($sets as $set) {

                    $quantity = count($set);

                    $charge = $set[0];

                    $surcharges = $this->db->join("charges", "charges.charge_id = quotation_surcharges.charge_id", "left")->get_where('quotation_surcharges', ["quotation_prices_id" => $charge->quotation_prices_id])->result();

                    foreach ($surcharges as $surcharge) {
                        $converted_rate = round($this->currency_convert_rate($rates, "USD", $charge->default_currency, $first_vessel_info[0]->exchange_rate), 2);

                        $this->db->reset_query();
                        $this->db->set("booking_id", $charge->booking_id);
                        if ($booking[0]->payment_terms == "Credit") {
                            $this->db->set("booking_payment_type", "Collect");
                            $this->db->set("collection_branch_id", $booking[0]->destination_branch_id);
                        } else {
                            $this->db->set("booking_payment_type", "Prepaid");
                        }

                        $this->db->set("exchange_rate", $converted_rate);
                        $this->db->set("booking_charge_currency", "USD");
                        $this->db->set("booking_charge_price", $surcharge->surcharge_price);
                        $this->db->set("quantity", $quantity);
                        $this->db->set("auto_added", 1);
                        $this->db->set("auto_charge_type", "freight");
                        $this->db->set("charges_remarks", $surcharge->charge_name . " for " . $quantity . "x" . $charge->typesize);
                        $this->db->insert("booking_charges");
                    }

                    $converted_rate = round($this->currency_convert_rate($rates, "USD", $charge->default_currency, $first_vessel_info[0]->exchange_rate), 2);

                    $this->db->reset_query();
                    $this->db->set("booking_id", $charge->booking_id);
                    if ($booking[0]->payment_terms == "Credit") {
                        $this->db->set("booking_payment_type", "Collect");
                        $this->db->set("collection_branch_id", $booking[0]->destination_branch_id);
                    } else {
                        $this->db->set("booking_payment_type", "Prepaid");
                    }

                    $this->db->set("exchange_rate", $converted_rate);
                    $this->db->set("booking_charge_currency", "USD");
                    $this->db->set("booking_charge_price", $charge->price);
                    $this->db->set("quantity", $quantity);
                    $this->db->set("auto_added", 1);
                    $this->db->set("auto_charge_type", "freight");
                    $this->db->set("charges_remarks", "Ocean freight charge for " . $quantity . "x" . $charge->typesize);
                    $this->db->insert("booking_charges");
                }
            }
        }
    }

    public function decimals($val)
    {
        return preg_replace("/([0]{2})$/", "", number_format($val, 4));
    }

    public function colours($i = null)
    {
        $colours = [];
        $colours['blue'] = '#007bff';
        $colours['indigo'] = '#6610f2';
        $colours['purple'] = '#6f42c1';
        $colours['pink'] = '#e83e8c';
        $colours['red'] = '#dc3545';
        $colours['orange'] = '#fd7e14';
        $colours['yellow'] = '#ffc107';
        $colours['green'] = '#28a745';
        $colours['teal'] = '#20c997';
        $colours['cyan'] = '#17a2b8';
        $colours['gray'] = '#6c757d';
        $colours['gray-dark'] = '#343a40';
        $colours['primary'] = '#007bff';
        $colours['secondary'] = '#6c757d';
        $colours['success'] = '#28a745';
        $colours['info'] = '#17a2b8';
        $colours['warning'] = '#ffc107';
        $colours['danger'] = '#dc3545';
        $colours['dark'] = '#343a40';

        if (isset($i)) {
            if ($i >= count($colours)) {
                $i = $i - count($colours);
            }
            $colours = array_values($colours);
            return $colours[$i];
        } else {
            return $colours;
        }
    }

    public function timezones()
    {
        return array(
            (object) ["id" => "Pacific/Midway", "name" => "(GMT-11:00) Midway Island"],
            (object) ["id" => "US/Samoa", "name" => "(GMT-11:00) Samoa"],
            (object) ["id" => "US/Hawaii", "name" => "(GMT-10:00) Hawaii"],
            (object) ["id" => "US/Alaska", "name" => "(GMT-09:00) Alaska"],
            (object) ["id" => "US/Pacific", "name" => "(GMT-08:00) Pacific Time (US &amp; Canada)"],
            (object) ["id" => "America/Tijuana", "name" => "(GMT-08:00) Tijuana"],
            (object) ["id" => "US/Arizona", "name" => "(GMT-07:00) Arizona"],
            (object) ["id" => "US/Mountain", "name" => "(GMT-07:00) Mountain Time (US &amp; Canada)"],
            (object) ["id" => "America/Chihuahua", "name" => "(GMT-07:00) Chihuahua"],
            (object) ["id" => "America/Mazatlan", "name" => "(GMT-07:00) Mazatlan"],
            (object) ["id" => "America/Mexico_City", "name" => "(GMT-06:00) Mexico City"],
            (object) ["id" => "America/Monterrey", "name" => "(GMT-06:00) Monterrey"],
            (object) ["id" => "Canada/Saskatchewan", "name" => "(GMT-06:00) Saskatchewan"],
            (object) ["id" => "US/Central", "name" => "(GMT-06:00) Central Time (US &amp; Canada)"],
            (object) ["id" => "US/Eastern", "name" => "(GMT-05:00) Eastern Time (US &amp; Canada)"],
            (object) ["id" => "US/East-Indiana", "name" => "(GMT-05:00) Indiana (East)"],
            (object) ["id" => "America/Bogota", "name" => "(GMT-05:00) Bogota"],
            (object) ["id" => "America/Lima", "name" => "(GMT-05:00) Lima"],
            (object) ["id" => "America/Caracas", "name" => "(GMT-04:30) Caracas"],
            (object) ["id" => "Canada/Atlantic", "name" => "(GMT-04:00) Atlantic Time (Canada)"],
            (object) ["id" => "America/La_Paz", "name" => "(GMT-04:00) La Paz"],
            (object) ["id" => "America/Santiago", "name" => "(GMT-04:00) Santiago"],
            (object) ["id" => "Canada/Newfoundland", "name" => "(GMT-03:30) Newfoundland"],
            (object) ["id" => "America/Buenos_Aires", "name" => "(GMT-03:00) Buenos Aires"],
            (object) ["id" => "Greenland", "name" => "(GMT-03:00) Greenland"],
            (object) ["id" => "Atlantic/Stanley", "name" => "(GMT-02:00) Stanley"],
            (object) ["id" => "Atlantic/Azores", "name" => "(GMT-01:00) Azores"],
            (object) ["id" => "Atlantic/Cape_Verde", "name" => "(GMT-01:00) Cape Verde Is."],
            (object) ["id" => "Africa/Casablanca", "name" => "(GMT) Casablanca"],
            (object) ["id" => "Europe/Dublin", "name" => "(GMT) Dublin"],
            (object) ["id" => "Europe/Lisbon", "name" => "(GMT) Lisbon"],
            (object) ["id" => "Europe/London", "name" => "(GMT) London"],
            (object) ["id" => "Africa/Monrovia", "name" => "(GMT) Monrovia"],
            (object) ["id" => "Europe/Amsterdam", "name" => "(GMT+01:00) Amsterdam"],
            (object) ["id" => "Europe/Belgrade", "name" => "(GMT+01:00) Belgrade"],
            (object) ["id" => "Europe/Berlin", "name" => "(GMT+01:00) Berlin"],
            (object) ["id" => "Europe/Bratislava", "name" => "(GMT+01:00) Bratislava"],
            (object) ["id" => "Europe/Brussels", "name" => "(GMT+01:00) Brussels"],
            (object) ["id" => "Europe/Budapest", "name" => "(GMT+01:00) Budapest"],
            (object) ["id" => "Europe/Copenhagen", "name" => "(GMT+01:00) Copenhagen"],
            (object) ["id" => "Europe/Ljubljana", "name" => "(GMT+01:00) Ljubljana"],
            (object) ["id" => "Europe/Madrid", "name" => "(GMT+01:00) Madrid"],
            (object) ["id" => "Europe/Paris", "name" => "(GMT+01:00) Paris"],
            (object) ["id" => "Europe/Prague", "name" => "(GMT+01:00) Prague"],
            (object) ["id" => "Europe/Rome", "name" => "(GMT+01:00) Rome"],
            (object) ["id" => "Europe/Sarajevo", "name" => "(GMT+01:00) Sarajevo"],
            (object) ["id" => "Europe/Skopje", "name" => "(GMT+01:00) Skopje"],
            (object) ["id" => "Europe/Stockholm", "name" => "(GMT+01:00) Stockholm"],
            (object) ["id" => "Europe/Vienna", "name" => "(GMT+01:00) Vienna"],
            (object) ["id" => "Europe/Warsaw", "name" => "(GMT+01:00) Warsaw"],
            (object) ["id" => "Europe/Zagreb", "name" => "(GMT+01:00) Zagreb"],
            (object) ["id" => "Europe/Athens", "name" => "(GMT+02:00) Athens"],
            (object) ["id" => "Europe/Bucharest", "name" => "(GMT+02:00) Bucharest"],
            (object) ["id" => "Africa/Cairo", "name" => "(GMT+02:00) Cairo"],
            (object) ["id" => "Africa/Harare", "name" => "(GMT+02:00) Harare"],
            (object) ["id" => "Europe/Helsinki", "name" => "(GMT+02:00) Helsinki"],
            (object) ["id" => "Europe/Istanbul", "name" => "(GMT+02:00) Istanbul"],
            (object) ["id" => "Asia/Jerusalem", "name" => "(GMT+02:00) Jerusalem"],
            (object) ["id" => "Europe/Kiev", "name" => "(GMT+02:00) Kyiv"],
            (object) ["id" => "Europe/Minsk", "name" => "(GMT+02:00) Minsk"],
            (object) ["id" => "Europe/Riga", "name" => "(GMT+02:00) Riga"],
            (object) ["id" => "Europe/Sofia", "name" => "(GMT+02:00) Sofia"],
            (object) ["id" => "Europe/Tallinn", "name" => "(GMT+02:00) Tallinn"],
            (object) ["id" => "Europe/Vilnius", "name" => "(GMT+02:00) Vilnius"],
            (object) ["id" => "Asia/Baghdad", "name" => "(GMT+03:00) Baghdad"],
            (object) ["id" => "Asia/Kuwait", "name" => "(GMT+03:00) Kuwait"],
            (object) ["id" => "Africa/Nairobi", "name" => "(GMT+03:00) Nairobi"],
            (object) ["id" => "Asia/Riyadh", "name" => "(GMT+03:00) Riyadh"],
            (object) ["id" => "Europe/Moscow", "name" => "(GMT+03:00) Moscow"],
            (object) ["id" => "Asia/Tehran", "name" => "(GMT+03:30) Tehran"],
            (object) ["id" => "Asia/Baku", "name" => "(GMT+04:00) Baku"],
            (object) ["id" => "Europe/Volgograd", "name" => "(GMT+04:00) Volgograd"],
            (object) ["id" => "Asia/Muscat", "name" => "(GMT+04:00) Muscat"],
            (object) ["id" => "Asia/Tbilisi", "name" => "(GMT+04:00) Tbilisi"],
            (object) ["id" => "Asia/Yerevan", "name" => "(GMT+04:00) Yerevan"],
            (object) ["id" => "Asia/Kabul", "name" => "(GMT+04:30) Kabul"],
            (object) ["id" => "Asia/Karachi", "name" => "(GMT+05:00) Karachi"],
            (object) ["id" => "Asia/Tashkent", "name" => "(GMT+05:00) Tashkent"],
            (object) ["id" => "Asia/Kolkata", "name" => "(GMT+05:30) Kolkata"],
            (object) ["id" => "Asia/Kathmandu", "name" => "(GMT+05:45) Kathmandu"],
            (object) ["id" => "Asia/Yekaterinburg", "name" => "(GMT+06:00) Ekaterinburg"],
            (object) ["id" => "Asia/Almaty", "name" => "(GMT+06:00) Almaty"],
            (object) ["id" => "Asia/Dhaka", "name" => "(GMT+06:00) Dhaka"],
            (object) ["id" => "Asia/Novosibirsk", "name" => "(GMT+07:00) Novosibirsk"],
            (object) ["id" => "Asia/Bangkok", "name" => "(GMT+07:00) Bangkok"],
            (object) ["id" => "Asia/Jakarta", "name" => "(GMT+07:00) Jakarta"],
            (object) ["id" => "Asia/Krasnoyarsk", "name" => "(GMT+08:00) Krasnoyarsk"],
            (object) ["id" => "Asia/Chongqing", "name" => "(GMT+08:00) Chongqing"],
            (object) ["id" => "Asia/Hong_Kong", "name" => "(GMT+08:00) Hong Kong"],
            (object) ["id" => "Asia/Kuala_Lumpur", "name" => "(GMT+08:00) Kuala Lumpur"],
            (object) ["id" => "Australia/Perth", "name" => "(GMT+08:00) Perth"],
            (object) ["id" => "Asia/Singapore", "name" => "(GMT+08:00) Singapore"],
            (object) ["id" => "Asia/Taipei", "name" => "(GMT+08:00) Taipei"],
            (object) ["id" => "Asia/Ulaanbaatar", "name" => "(GMT+08:00) Ulaan Bataar"],
            (object) ["id" => "Asia/Urumqi", "name" => "(GMT+08:00) Urumqi"],
            (object) ["id" => "Asia/Irkutsk", "name" => "(GMT+09:00) Irkutsk"],
            (object) ["id" => "Asia/Seoul", "name" => "(GMT+09:00) Seoul"],
            (object) ["id" => "Asia/Tokyo", "name" => "(GMT+09:00) Tokyo"],
            (object) ["id" => "Australia/Adelaide", "name" => "(GMT+09:30) Adelaide"],
            (object) ["id" => "Australia/Darwin", "name" => "(GMT+09:30) Darwin"],
            (object) ["id" => "Asia/Yakutsk", "name" => "(GMT+10:00) Yakutsk"],
            (object) ["id" => "Australia/Brisbane", "name" => "(GMT+10:00) Brisbane"],
            (object) ["id" => "Australia/Canberra", "name" => "(GMT+10:00) Canberra"],
            (object) ["id" => "Pacific/Guam", "name" => "(GMT+10:00) Guam"],
            (object) ["id" => "Australia/Hobart", "name" => "(GMT+10:00) Hobart"],
            (object) ["id" => "Australia/Melbourne", "name" => "(GMT+10:00) Melbourne"],
            (object) ["id" => "Pacific/Port_Moresby", "name" => "(GMT+10:00) Port Moresby"],
            (object) ["id" => "Australia/Sydney", "name" => "(GMT+10:00) Sydney"],
            (object) ["id" => "Asia/Vladivostok", "name" => "(GMT+11:00) Vladivostok"],
            (object) ["id" => "Asia/Magadan", "name" => "(GMT+12:00) Magadan"],
            (object) ["id" => "Pacific/Auckland", "name" => "(GMT+12:00) Auckland"],
            (object) ["id" => "Pacific/Fiji", "name" => "(GMT+12:00) Fiji"],
        );
    }
    public function time_elapsed_string($datetime, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) {
            $string = array_slice($string, 0, 1);
        }

        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }



    public function billing_type_icon($type)
    {
        switch ($type) {
            case 'workers':
                return '<i class="fas fa-fw fa-user-cog tip" title="Workers"></i>';
            case 'equipments':
                return '<i class="fas fa-fw fa-tools tip" title="Equipments"></i>';
            case 'gears':
                return '<i class="fas fa-fw fa-cogs tip" title="Gears"></i>';
            case 'delay':
                return '<i class="fas fa-fw fa-hourglass-half tip" title="Delays"></i>';
            case 'work_meal':
                return '<i class="fas fa-fw fa-utensils tip" title="Work through meal"></i>';
            case 'disposal':
                return '<i class="fas fa-fw fa-recycle tip" title="Disposal activity"></i>';
            case 'disposal_weight':
                return '<span class="tip" title="Disposal activity weight"><i class="fas fa-fw fa-recycle"></i><i class="fa fa-weight-hanging"></i></span>';
            case 'commodity':
                return '<i class="fas fa-fw fa-truck-loading tip" title="Commodity based billing"></i>';
        }
    }
}
