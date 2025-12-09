package com.dhanuka.indhan.DataBase;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;

import com.dhanuka.indhan.ATPMenu.activity.ATPBean;
import com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.SupervisorActivateBean;
import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryAtpResponse;
import com.dhanuka.indhan.DataBase.TableDataModel.ArrivalDepartureModelOffline;
import com.google.gson.Gson;

import org.jetbrains.annotations.NotNull;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by ashutosh on 3/21/2018.
 */
public class DataBaseHelperNew extends SQLiteOpenHelper {

    public static final String DATABASE_NAME = "SQLiteDatabase.db";
    public static final String ATP_HISTORY_TABLE = "ATP_HISTORY";
    public static final String ATP_ACTIVE_TABLE = "ATP_ACTIVE";
    public static final String MY_ATP_TABLE = "MY_ATP";
    public static final String ATP_LIST = "ATP_LIST";
    public static final String SELF_PARTICIPATION = "SELF_PARTICIPATION";
    public static final String ID = "id";
    public static final String TICKET = "TICKET";
    public static final String TICKET_ID = "ticket_ID";
    public static final String IMAGE_URL = "image_url";
    public static final String COMMENT = "comment";
    public static final String TOKEN = "token";
    public static final String LATITUTE = "latitude";
    public static final String LONGITUDE = "longtitude";
    public static final String DATAJSON = "dataJson";
    public static final String IS_UPLOADED = "";
    public static final String CREATE_QUERY =
            "CREATE TABLE IF NOT EXISTS " + ATP_HISTORY_TABLE + "("
                    + ID + " INTEGER  PRIMARY KEY AUTOINCREMENT,"
                    + TICKET + " TEXT " + ")";
    public static final String CREATE_ACTIVE_ATP_TABLE =
            "CREATE TABLE IF NOT EXISTS " + ATP_ACTIVE_TABLE + "("
                    + ID + " INTEGER  PRIMARY KEY AUTOINCREMENT,"
                    + TICKET + " TEXT " + ")";
    public static final String CREATE_MY_ATP_TABLE =
            "CREATE TABLE IF NOT EXISTS " + MY_ATP_TABLE + "("
                    + ID + " INTEGER  PRIMARY KEY AUTOINCREMENT,"
                    + TICKET + " TEXT " + ")";
    public static final String CREATE_ATP_TABLE =
            "CREATE TABLE IF NOT EXISTS " + ATP_LIST + "("
                    + ID + " INTEGER  PRIMARY KEY AUTOINCREMENT,"
                    + TICKET + " TEXT " + ")";
    //    public static final String CREATE_SELF_PARTICIPATION =
//            "CREATE TABLE IF NOT EXISTS " + SELF_PARTICIPATION + "("
//                    + TICKET_ID + " INTEGER,"
//                    + TOKEN + " TEXT ,"
//                    + COMMENT + " TEXT , "
//                    + IMAGE_URL + " TEXT , "
//                    + LATITUTE + " TEXT , "
//                    + LONGITUDE + " TEXT , "
//                    + DATAJSON + " TEXT , "
//                    + IS_UPLOADED + " TEXT  " + ")";
    private static final int DATABASE_VERSION = 2;


    public DataBaseHelperNew(Context context) {
        super(context, DATABASE_NAME, null, DATABASE_VERSION);
    }

    @Override
    public void onCreate(SQLiteDatabase db) {
        db.execSQL(CREATE_QUERY);
        db.execSQL(CREATE_ACTIVE_ATP_TABLE);
        db.execSQL(CREATE_MY_ATP_TABLE);
        db.execSQL(CREATE_ATP_TABLE);
//        db.execSQL(CREATE_SELF_PARTICIPATION);


    }

    @Override
    public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
        db.execSQL("DROP TABLE IF EXISTS " + ATP_HISTORY_TABLE);
        db.execSQL("DROP TABLE IF EXISTS " + ATP_ACTIVE_TABLE);
        db.execSQL("DROP TABLE IF EXISTS " + MY_ATP_TABLE);
        db.execSQL("DROP TABLE IF EXISTS " + ATP_LIST);
//        db.execSQL("DROP TABLE IF EXISTS " + SELF_PARTICIPATION);

        onCreate(db);
    }

    public void addSelfParticipation(ArrivalDepartureModelOffline model) {
//        SQLiteDatabase db1 = this.getWritableDatabase();
//        ContentValues values = new ContentValues();
//        values.put(TICKET_ID, model.getTicket_id());
//        values.put(COMMENT, model.getComment());
//        values.put(IMAGE_URL, model.getImageURL());
//        values.put(TOKEN, model.getToken());
//        values.put(LATITUTE, model.getLatitude());
//        values.put(LONGITUDE, model.getLongtitude());
//        values.put(DATAJSON, model.getDataJson());
//        db1.insert(SELF_PARTICIPATION, null, values);
//        db1.close();
    }


    /*Add data in history*/
    public void AddDataInBox(Context context, TicketHistoryAtpResponse data) {
        SQLiteDatabase db1 = this.getWritableDatabase();
        String sql = "DELETE FROM " + ATP_HISTORY_TABLE;
        db1.execSQL(sql);
        for (int i = 0; i < data.getData().size(); i++) {
            SQLiteDatabase db = this.getWritableDatabase();
            ContentValues values = new ContentValues();
            values.put(TICKET, new Gson().toJson(data.getData().get(i)));
            db.insert(ATP_HISTORY_TABLE, null, values);
            db.close();
        }


    }

    /*Add data in MY ATP*/
    public void AddDataInBox(Context context, ATPBean data) {
        SQLiteDatabase db1 = this.getWritableDatabase();
        String sql = "DELETE FROM " + MY_ATP_TABLE;
        db1.execSQL(sql);
        for (int i = 0; i < data.getData().size(); i++) {
            SQLiteDatabase db = this.getWritableDatabase();
            ContentValues values = new ContentValues();
            values.put(TICKET, new Gson().toJson(data.getData().get(i)));
            db.insert(MY_ATP_TABLE, null, values);
            db.close();
        }


    }

    /*Add data in ATP list*/
    public void addATPLIST(Context context, ATPBean data) {
        SQLiteDatabase db1 = this.getWritableDatabase();
        String sql = "DELETE FROM " + ATP_LIST;
        db1.execSQL(sql);
        for (int i = 0; i < data.getData().size(); i++) {
            SQLiteDatabase db = this.getWritableDatabase();
            ContentValues values = new ContentValues();
            values.put(TICKET, new Gson().toJson(data.getData().get(i)));
            db.insert(ATP_LIST, null, values);
            db.close();
        }


    }

    /*Add data in ATP list*/
    public void addATPLISTWithoutClearing(Context context, ATPBean data) {
        SQLiteDatabase db1 = this.getWritableDatabase();
        String sql = "DELETE FROM " + ATP_LIST;
//        db1.execSQL(sql);
        for (int i = 0; i < data.getData().size(); i++) {
            SQLiteDatabase db = this.getWritableDatabase();
            ContentValues values = new ContentValues();
            values.put(TICKET, new Gson().toJson(data.getData().get(i)));
            db.insert(ATP_LIST, null, values);
            db.close();
        }


    }

    /*Delet data in ATP list*/
    public void DelATPLISTWithoutClearing(Context context, ATPBean data) {
        SQLiteDatabase db1 = this.getWritableDatabase();
        String sql = "DELETE FROM " + ATP_LIST;
        db1.execSQL(sql);
//        for (int i = 0; i < data.getData().size(); i++) {
//            SQLiteDatabase db = this.getWritableDatabase();
//            ContentValues values = new ContentValues();
//            values.put(TICKET, new Gson().toJson(data.getData().get(i)));
//            db.insert(ATP_LIST, null, values);
//            db.close();
//        }


    }

    //*****************get data from DB for history**********************//
    public TicketHistoryAtpResponse getATPHistory(Context context) {
        TicketHistoryAtpResponse data = new TicketHistoryAtpResponse();
        data.setMessage("");
        data.setStatus("200");
        data.setStatus_code(200);

        List<TicketHistoryAtpResponse.Data> datumList = new ArrayList<>();

        SQLiteDatabase myDataBase = this.getWritableDatabase();
        String searchQuery = "SELECT  * FROM " + ATP_HISTORY_TABLE;
        Cursor cursor = myDataBase.rawQuery(searchQuery, null);

        cursor.moveToFirst();
        while (cursor.isAfterLast() == false) {
            datumList.add(new Gson().fromJson(cursor.getString(1), TicketHistoryAtpResponse.Data.class));
            cursor.moveToNext();
        }
        data.setData(datumList);
        cursor.close();
        return data;


    }

    //*****************get data from DB for active atp**********************//
    public SupervisorActivateBean getActiveATP(Context context) {
        List<SupervisorActivateBean.Data> datumList = new ArrayList<>();

        SQLiteDatabase myDataBase = this.getWritableDatabase();
        String searchQuery = "SELECT  * FROM " + ATP_ACTIVE_TABLE;
        Cursor cursor = myDataBase.rawQuery(searchQuery, null);

        cursor.moveToFirst();
        while (cursor.isAfterLast() == false) {
            datumList.add(new Gson().fromJson(cursor.getString(1), SupervisorActivateBean.Data.class));
            cursor.moveToNext();
        }
        SupervisorActivateBean data = new SupervisorActivateBean(datumList, "", "200", 200);
        cursor.close();
        return data;


    }


    //*****************get data from DB for active atp**********************//
    public ATPBean getMyATP(Context context) {
        ArrayList<ATPBean.Data> datumList = new ArrayList<>();

        SQLiteDatabase myDataBase = this.getWritableDatabase();
        String searchQuery = "SELECT  * FROM " + MY_ATP_TABLE;
        Cursor cursor = myDataBase.rawQuery(searchQuery, null);

        cursor.moveToFirst();
        while (cursor.isAfterLast() == false) {
            datumList.add(new Gson().fromJson(cursor.getString(1), ATPBean.Data.class));
            cursor.moveToNext();
        }
        ATPBean data = new ATPBean();
        data.setData(datumList);
        data.setMessage("");
        data.setStatus("200");
        data.setStatus_code(200);
        cursor.close();
        return data;


    }

    //*****************get data from DB for active atp**********************//
    public ATPBean getATPList(Context context) {
        ArrayList<ATPBean.Data> datumList = new ArrayList<>();

        SQLiteDatabase myDataBase = this.getWritableDatabase();
        String searchQuery = "SELECT  * FROM " + ATP_LIST;
        Cursor cursor = myDataBase.rawQuery(searchQuery, null);

        cursor.moveToFirst();
        while (cursor.isAfterLast() == false) {
            datumList.add(new Gson().fromJson(cursor.getString(1), ATPBean.Data.class));
            cursor.moveToNext();
        }
        ATPBean data = new ATPBean();
        data.setData(datumList);
        data.setMessage("");
        data.setStatus("200");
        data.setStatus_code(200);
        cursor.close();
        return data;


    }


    //*****************get data from DB for active atp**********************//
    public ArrayList<ArrivalDepartureModelOffline> GetAllParticipation() {
        ArrayList<ArrivalDepartureModelOffline> arrayList = new ArrayList<>();
//        SQLiteDatabase myDataBase = this.getWritableDatabase();
//        String searchQuery = "SELECT  * FROM " + SELF_PARTICIPATION;
//        Cursor cursor = myDataBase.rawQuery(searchQuery, null);
//        cursor.moveToFirst();
//
//        while (!cursor.isAfterLast()) {
//            SelfParticipationMOffline mOffline = new SelfParticipationMOffline();
//
//            try {
//                mOffline.setTicket_id(cursor.getString(0));
//                mOffline.setToken(cursor.getString(1));
//                mOffline.setComment(cursor.getString(2));
//                mOffline.setImageURL(cursor.getString(3));
//                mOffline.setLatitude(cursor.getString(4));
//                mOffline.setLongtitude(cursor.getString(5));
//                mOffline.setDataJson(cursor.getString(6));
//
//            } catch (Exception e) {
//            }
//
//            arrayList.add(mOffline);
//            cursor.moveToNext();
//
//        }
//        cursor.close();
        return arrayList;


    }

    public void deleteAllParticipation() {
        //        SQLiteDatabase db1 = this.getWritableDatabase();
        //        String sql = "DELETE FROM " + SELF_PARTICIPATION;
        //        db1.execSQL(sql);
    }

    /*Add data in active tickets*/
    public void AddDataInActiveTableBox(@NotNull Context requireContext,
                                        @NotNull List<SupervisorActivateBean.Data> data) {
        SQLiteDatabase db1 = this.getWritableDatabase();
        String sql = "DELETE FROM " + ATP_ACTIVE_TABLE;
        db1.execSQL(sql);
        for (int i = 0; i < data.size(); i++) {
            SQLiteDatabase db = this.getWritableDatabase();
            ContentValues values = new ContentValues();
            values.put(TICKET, new Gson().toJson(data.get(i)));
            db.insert(ATP_ACTIVE_TABLE, null, values);
            db.close();
        }
    }
}
