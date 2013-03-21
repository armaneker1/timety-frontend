<?php

class RemoveItemByIdReturnItem extends Predis\Command\ScriptedCommand {

    public function getKeysCount() {
        return -1;
    }

    public function getScript() {
        return <<<LUA
    local key_id = KEYS[1]
    local rem_id = tonumber(ARGV[1])
    local data = redis.call('zrange',key_id,0,-1)
    local obj
    local result = 0
    for k,v in pairs(data) do
        obj = cjson.decode(v)
        if tostring(obj.id) == tostring(rem_id) then
            redis.call('zrem',key_id,v)
            result = v
            break
        end
    end
    return result
LUA;
    }

}
?>
