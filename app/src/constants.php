<?php

#Costanti directory
const DIR_SEPARATOR = "/";
const APP = "app" . DIR_SEPARATOR;
const CONTROLLER = APP . "controllers" . DIR_SEPARATOR;
const MODELS = APP . "models" . DIR_SEPARATOR;
const HELPERS = APP . "helpers" . DIR_SEPARATOR;
const VIEWS = APP . "views" . DIR_SEPARATOR;
const CONFIGS = APP . "configs" . DIR_SEPARATOR;
const SETTING = "settings" . DIR_SEPARATOR;

#Costanti namespace
const NAMESPACE_SEPARATOR = "\\";
const APP_NAMESPACE = "App" . NAMESPACE_SEPARATOR;
const CONTROLLER_NAMESPACE = APP_NAMESPACE . "Controllers" . NAMESPACE_SEPARATOR;
const MODELS_NAMESPACE = APP_NAMESPACE . "Models" . NAMESPACE_SEPARATOR;

#Files
const SERVERS_FILE = SETTING . "servers.json";