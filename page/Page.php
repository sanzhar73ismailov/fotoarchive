<?php

	class Page
	{
		protected $f3 = null;

		public function __construct($f3)
		{
			$this->f3 = $f3;
			$f3->set('base_href', $f3->get('config')['base_href']);
		}

		protected function resolveParam($param, $default)
		{
			if ($this->f3->exists('PARAMS.' . $param))
				return $this->f3->get('PARAMS.' . $param);
			return $default;
		}
	}