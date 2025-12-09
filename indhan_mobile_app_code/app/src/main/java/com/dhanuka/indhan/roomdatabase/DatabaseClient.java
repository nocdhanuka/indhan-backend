package com.dhanuka.indhan.roomdatabase;

import android.content.Context;

import androidx.room.Room;

import com.dhanuka.indhan.Constants.Constants;

public class DatabaseClient {

    private static DatabaseClient mInstance;
    private Context mCtx;
    //our app database object
    private AppDatabase appDatabase;

    private DatabaseClient(Context mCtx) {
        this.mCtx = mCtx;

        //creating the app database with Room database builder
        String DB_NAME = Constants.INSTANCE.getROOM_DATABASE_NAME();
        appDatabase = Room.databaseBuilder(mCtx, AppDatabase.class, DB_NAME).build();
    }

    public AppDatabase getAppDatabase() {
        return appDatabase;
    }

    public static synchronized DatabaseClient getInstance(Context mCtx) {
        if (mInstance == null) {
            mInstance = new DatabaseClient(mCtx);
        }
        return mInstance;
    }
}