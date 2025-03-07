<?php

namespace Dejurin;

/**
 * CodeIgniter.
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2019 - 2022, CodeIgniter Foundation
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @copyright	Copyright (c) 2019 - 2022, CodeIgniter Foundation (https://codeigniter.com/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 *
 * @see	https://codeigniter.com
 * @since	Version 1.3.1
 * @filesource
 */
// defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * HTML Table Generating Class.
 *
 * Lets you create tables manually or from database result objects, or arrays.
 *
 * @category	HTML Tables
 *
 * @author		EllisLab Dev Team
 *
 * @see		https://codeigniter.com/userguide3/libraries/table.html
 */

/**
 * PHP Table Generate.
 *
 * Simple PHP HTML table generator by CodeIgniter.
 *
 * @category	HTML Tables
 *
 * @author		YURII DARWIN
 *
 * @see		https://github.com/dejurin/php-table-generate
 * @since Version 1.0.0
 */
class PHPTableGenerate
{
    /**
     * Data for table rows.
     *
     * @var array
     */
    public $rows = [];

    /**
     * Data for table heading.
     *
     * @var array
     */
    public $heading = [];

    /**
     * Whether or not to automatically create the table header.
     *
     * @var bool
     */
    public $auto_heading = true;

    /**
     * Table caption.
     *
     * @var string
     */
    public $caption = null;

    /**
     * Table layout template.
     *
     * @var array
     */
    public $template = null;

    /**
     * Newline setting.
     *
     * @var string
     */
    public $newline = "\n";

    /**
     * Contents of empty cells.
     *
     * @var string
     */
    public $empty_cells = '';

    /**
     * Callback for custom table layout.
     *
     * @var function
     */
    public $function = null;

    /**
     * Template.
     *
     * @var array
     */
    protected $temp = [];

    /**
     * Set the template.
     *
     * @param array $template
     *
     * @return bool
     */
    public function set_template($template)
    {
        if (!is_array($template)) {
            return false;
        }

        $this->template = $template;

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Set the table heading.
     *
     * Can be passed as an array or discreet params
     *
     * @param	mixed
     *
     * @return CI_Table
     */
    public function set_heading($args = [])
    {
        $this->heading = $this->_prep_args(func_get_args());

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Set columns. Takes a one-dimensional array as input and creates
     * a multi-dimensional array with a depth equal to the number of
     * columns. This allows a single array with many elements to be
     * displayed in a table that has a fixed column count.
     *
     * @param array $array
     * @param int   $col_limit
     *
     * @return array
     */
    public function make_columns($array = [], $col_limit = 0)
    {
        if (!is_array($array) or 0 === count($array) or !is_int($col_limit)) {
            return false;
        }

        // Turn off the auto-heading feature since it's doubtful we
        // will want headings from a one-dimensional array
        $this->auto_heading = false;

        if (0 === $col_limit) {
            return $array;
        }

        $new = [];
        do {
            $temp = array_splice($array, 0, $col_limit);

            if (count($temp) < $col_limit) {
                for ($i = count($temp); $i < $col_limit; ++$i) {
                    $temp[] = '&nbsp;';
                }
            }

            $new[] = $temp;
        } while (count($array) > 0);

        return $new;
    }

    // --------------------------------------------------------------------

    /**
     * Set "empty" cells.
     *
     * Can be passed as an array or discreet params
     *
     * @param mixed $value
     *
     * @return CI_Table
     */
    public function set_empty($value)
    {
        $this->empty_cells = $value;

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Add a table row.
     *
     * Can be passed as an array or discreet params
     *
     * @param	mixed
     *
     * @return CI_Table
     */
    public function add_row($args = [])
    {
        $this->rows[] = $this->_prep_args(func_get_args());

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Prep Args.
     *
     * Ensures a standard associative array format for all cell data
     *
     * @param	array
     *
     * @return array
     */
    protected function _prep_args($args)
    {
        // If there is no $args[0], skip this and treat as an associative array
        // This can happen if there is only a single key, for example this is passed to table->generate
        // array(array('foo'=>'bar'))
        if (isset($args[0]) && 1 === count($args) && is_array($args[0]) && !isset($args[0]['data'])) {
            $args = $args[0];
        }

        foreach ($args as $key => $val) {
            is_array($val) or $args[$key] = ['data' => $val];
        }

        return $args;
    }

    // --------------------------------------------------------------------

    /**
     * Add a table caption.
     *
     * @param string $caption
     *
     * @return CI_Table
     */
    public function set_caption($caption)
    {
        $this->caption = $caption;

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Generate the table.
     *
     * @param mixed $table_data
     *
     * @return string
     */
    public function generate($table_data = null)
    {
        // The table data can optionally be passed to this function
        // either as a database result object or an array
        if (!empty($table_data)) {
            if (is_array($table_data)) {
                $this->_set_from_array($table_data);
            }
        }

        // Is there anything to display? No? Smite them!
        if (empty($this->heading) && empty($this->rows)) {
            return 'Undefined table data';
        }

        // Compile and validate the template date
        $this->_compile_template();

        // Validate a possibly existing custom cell manipulation function
        if (isset($this->function) && !is_callable($this->function)) {
            $this->function = null;
        }

        // Build the table!

        $out = $this->template['table_open'].$this->newline;

        // Add any caption here
        if ($this->caption) {
            $out .= '<caption>'.$this->caption.'</caption>'.$this->newline;
        }

        // Is there a table heading to display?
        if (!empty($this->heading)) {
            $out .= $this->template['thead_open'].$this->newline.$this->template['heading_row_start'].$this->newline;

            foreach ($this->heading as $heading) {
                $temp = $this->template['heading_cell_start'];

                foreach ($heading as $key => $val) {
                    if ('data' !== $key) {
                        $temp = str_replace('<th', '<th '.$key.'="'.$val.'"', $temp);
                    }
                }

                $out .= $temp.(isset($heading['data']) ? $heading['data'] : '').$this->template['heading_cell_end'];
            }

            $out .= $this->template['heading_row_end'].$this->newline.$this->template['thead_close'].$this->newline;
        }

        // Build the table rows
        if (!empty($this->rows)) {
            $out .= $this->template['tbody_open'].$this->newline;

            $i = 1;
            foreach ($this->rows as $row) {
                if (!is_array($row)) {
                    break;
                }

                // We use modulus to alternate the row colors
                $name = fmod($i++, 2) ? '' : 'alt_';

                $out .= $this->template['row_'.$name.'start'].$this->newline;

                foreach ($row as $cell) {
                    $temp = $this->template['cell_'.$name.'start'];

                    foreach ($cell as $key => $val) {
                        if ('data' !== $key && 'td' !== $key) {
                            $temp = str_replace('<td', '<td '.$key.'="'.$val.'"', $temp);
                        }
                    }

                    if (isset($cell['td'])) {
                        $temp = str_replace('<td ', '<th ', $temp);
                    }

                    $cell = isset($cell['data']) ? $cell['data'] : '';
                    $out .= $temp;

                    if ('' === $cell or null === $cell) {
                        $out .= $this->empty_cells;
                    } elseif (isset($this->function)) {
                        $out .= call_user_func($this->function, $cell);
                    } else {
                        $out .= $cell;
                    }

                    $out .= ('<th' === substr($temp, 0, 3)) ? $this->template['heading_cell_end'] : $this->template['cell_'.$name.'end'];
                }

                $out .= $this->template['row_'.$name.'end'].$this->newline;
            }

            $out .= $this->template['tbody_close'].$this->newline;
        }

        $out .= $this->template['table_close'];

        // Clear table class properties before generating the table
        $this->clear();

        return $out;
    }

    // --------------------------------------------------------------------

    /**
     * Clears the table arrays.  Useful if multiple tables are being generated.
     *
     * @return CI_Table
     */
    public function clear()
    {
        $this->rows = [];
        $this->heading = [];
        $this->auto_heading = true;
        $this->caption = null;

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Set table data from an array.
     *
     * @param array $data
     *
     * @return void
     */
    protected function _set_from_array($data)
    {
        if (true === $this->auto_heading && empty($this->heading)) {
            $this->heading = $this->_prep_args(array_shift($data));
        }

        foreach ($data as &$row) {
            $this->rows[] = $this->_prep_args($row);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Compile Template.
     *
     * @return void
     */
    protected function _compile_template()
    {
        if (null === $this->template) {
            $this->template = $this->_default_template();

            return;
        }

        $this->temp = $this->_default_template();
        foreach (['table_open', 'thead_open', 'thead_close', 'heading_row_start', 'heading_row_end', 'heading_cell_start', 'heading_cell_end', 'tbody_open', 'tbody_close', 'row_start', 'row_end', 'cell_start', 'cell_end', 'row_alt_start', 'row_alt_end', 'cell_alt_start', 'cell_alt_end', 'table_close'] as $val) {
            if (!isset($this->template[$val])) {
                $this->template[$val] = $this->temp[$val];
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Default Template.
     *
     * @return array
     */
    protected function _default_template()
    {
        return [
            'table_open' => '<table border="0" cellpadding="4" cellspacing="0">',

            'thead_open' => '<thead>',
            'thead_close' => '</thead>',

            'heading_row_start' => '<tr>',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th>',
            'heading_cell_end' => '</th>',

            'tbody_open' => '<tbody>',
            'tbody_close' => '</tbody>',

            'row_start' => '<tr>',
            'row_end' => '</tr>',
            'cell_start' => '<td>',
            'cell_end' => '</td>',

            'row_alt_start' => '<tr>',
            'row_alt_end' => '</tr>',
            'cell_alt_start' => '<td>',
            'cell_alt_end' => '</td>',

            'table_close' => '</table>',
        ];
    }
}
