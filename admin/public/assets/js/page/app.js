var YNF_CONFIG = {};
(function(win, $, c) {
  c.ynf_table_nav = {}; //配置 表格导航 搜索 查询 
  c.ynf_table_nav.filter = {};


  c.tip = {};
  c.tip.no_data = "无数据";
  c.tip.no_val = "请输入值哦，亲亲！";
  c.tip.no_search_select = "请选择搜索项，亲亲！";
  c.tip.no_all_time_range = "请选择完整的时间范围";
  c.tip.sure_change_title = "确认更改";
  c.tip.no_ajax_data = "没有可提交的ajax数据";
  c.tip.submiting = "提交中";
  c.tip.submiting_success = "提交成功";
  c.tip.submiting_fail = "提交失败";
  c.tip.endtime_lt_starttime = "结束时间小于开始时间";


  c.link = {};
  c.link["void"] = "javascript:void(0)";


})(window, $, YNF_CONFIG);


//js 文件路径配置 
seajs.config({
  // Sea.js 的基础路径
  base: './assets/', // dev
  // 别名配置
  alias: {
    // seajs 插件
    "seajs_css": "js/seajs-2.3.0/seajs-css.js",
    "add_order": "js/page/add_order.js",
    // 文件上传模块 
    "dropzone": "js/dropzone.min.js",
  },

  // 变量配置
  vars: {
    'locale': 'zh-cn'
  },

  // 预加载项
  preload: [
    "seajs_css"
  ],

  // 调试模式
  debug: true,


  // 文件编码
  charset: 'utf-8'
});