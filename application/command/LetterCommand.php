<?php

namespace app\command;

use app\api\model\Letter;
use app\api\model\Setting;
use app\api\service\SmsService;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class LetterCommand extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('letter')
            ->setDescription('the letter command');
    }

    protected function execute(Input $input, Output $output)
    {
        $time = date('Y-m-d H:i:s');
        try {
            $setting = new Setting();
            $setting::$wxapp_id = null;
            $data = $setting->where('key','store')->select();
            if(count($data))
            {
                foreach ($data as $v)
                {
                    if($v)
                    {
                        $config = $v['values'];
                        $this->letter($config,$v['wxapp_id']);
                    }
                }
            }
        }catch (\Exception $exception)
        {
            log_write_task([
                'act'   => 'letter 定时任务',
                'error' => $exception->getMessage(),
                'line'  => $exception->getLine()
            ]);
            $output->writeln($time.$exception->getMessage().'|'.$exception->getFile().'|'.$exception->getLine());
        }
        // 指令输出
        $output->writeln($time.':letter');
    }

    /**
     * 短信发送定时任务
     * @param $config
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function letter($config,$wxapp_id)
    {
        $wxapp_id = $wxapp_id;
        $where = [];
        $where['wxapp_id'] = $wxapp_id;
        $where['pay_statu'] = 2;
        $where['send_sum'] = ['>',0];
        $where['is_complete'] = 0;
        $where['start_time'] = ['<',time()];
        $model = new Letter();
        $model::$wxapp_id = null;
        $data = $model->where($where)->select();
        $len = count($data);
        if($len)
        {
            log_write_task([
                'act'      => '短信发送定时任务',
                'wxapp_id' => $wxapp_id,
                'len'      => $len
            ]);
            foreach ($data as $v)
            {
                $day = $v['day'];
                // 大于1的判断今天是否还要发送
                if($day > 1)
                {
                    // 从开始到现在的天数
                    $t = ceil(time()-$v['start_time'] / (60*60*24));
                    // 天数 大于 已完成数量
                    if($t > $v['com_sum'])
                    {
                        SmsService::dataSend($v,$config);
                    }
                }
                else
                {
                    SmsService::dataSend($v,$config);
                }
            }
        }
    }
}