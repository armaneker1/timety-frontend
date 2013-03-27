<?php

class SeacrhUserById extends Predis\Command\ScriptedCommand {

    public function getKeysCount() {
        return -1;
    }

    public function getScript() {
        return <<<LUA
    local key_id = KEYS[1]
    local usr_id = ARGV[1]
    local data = redis.call('zrange',key_id,0,-1)
    local result
    local obj
    for k,v in pairs(data) do
        obj = cjson.decode(v)
        if tostring(obj.id)==tostring(usr_id) then
            result = v
            break
        end 
    end
    return result
LUA;
    }

}

?>
