package com.dhanuka.indhan.DataBase.DbStructure;

import android.database.ContentObserver;

/**
 * Created by John on 5/22/2018.
 */
public class MyContentObserver extends ContentObserver {

    DbValueChangeListener listener;

    public MyContentObserver() {
        super(null);
    }

    public void setListener(DbValueChangeListener listener) {
        this.listener = listener;
    }

    @Override
    public boolean deliverSelfNotifications() {
        return super.deliverSelfNotifications();
    }

    @Override
    public void onChange(boolean selfChange) {
        super.onChange(selfChange);
        listener.OnDbValueChange();
    }

    public interface DbValueChangeListener {
        void OnDbValueChange();
    }

}
