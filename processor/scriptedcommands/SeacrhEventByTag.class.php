<?php

class SeacrhEventByTag extends Predis\Command\ScriptedCommand {

    public function getKeysCount() {
        // Tell Predis to use all the arguments but the last one as arguments
        // for KEYS. The last one will be used to populate ARGV.
        return -1;
    }

    public function getScript() {
        return <<<LUA
    local key_id = KEYS[1]
    local tag_ids = cjson.decode(KEYS[2])
    local time = KEYS[3]
    local data = redis.call('zrangebyscore',key_id,time,'+inf')
    local obj
    local result = {}
    local indx = 0
    local found =0
    for k,v in pairs(data) do
        found =0
        obj = cjson.decode(v)
        for k1,v1 in pairs(tag_ids) do
            for k2,v2 in pairs(obj.tags) do
                if(tonumber(v2) == tonumber(v1)) then
                    found =1
                    break
                end
            end
            if(found==1) then 
                break
            end 
        end
        if found==1 then
            result[indx] = v
            indx=indx +1 
        end 
    end
    return result
LUA;
    }

}

?>
