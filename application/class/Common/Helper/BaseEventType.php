<?php

namespace Common\Helper;

class BaseEventType
{
    const DB_STARTED = 'DB.STARTED';
    const DB_COMPLETED = 'DB.COMPLETED';
    const DB_ROLLEDBACK = 'DB.ROLLEDBACK';

    const DB_PRE_UPDATE = 'DB.PRE.UPDATE';
    const DB_POST_UPDATE = 'DB.POST.UPDATE';
    const DB_PRE_INSERT = 'DB.PRE.INSERT';
    const DB_POST_INSERT = 'DB.POST.INSERT';
    const DB_PRE_DELETE = 'DB.PRE.DELETE';
    const DB_POST_DELETE = 'DB.POST.DELETE';
}
