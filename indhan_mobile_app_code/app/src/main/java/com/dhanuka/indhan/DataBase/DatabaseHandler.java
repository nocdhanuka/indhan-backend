package com.dhanuka.indhan.DataBase;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.util.Log;

import com.dhanuka.indhan.Model.RegisterFarmerContactModel;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by Shivam on 3/21/2018.
 */

public class DatabaseHandler extends SQLiteOpenHelper {

    // All Static variables
    // Database Version
    private static final int DATABASE_VERSION = 2;

    // Database Name
    private static final String DATABASE_NAME = "contactsManager";

    // Contacts table time
    private static final String TABLE_CONTACTS = "contacts";

    // Contacts Table Columns times
    private static final String KEY_AUTO_ID = "id";
    private static final String KEY_TICKET = "ticketNumber";
    private static final String KEY_NAME = "contactName";
    private static final String KEY_PH_NO = "phoneNumber";
    private static final String KEY_TIMESTAMP = "timestamp";
    private static final String KEY_IS_SYNC = "isSync";
    private static final String KEY_IS_DELETED = "isDeleted";
    private static final String KEY_TYPE = "type";
    private static final String KEY_LAST_MODIFIED = "lastModified";

    public DatabaseHandler(Context context) {
        super(context, DATABASE_NAME, null, DATABASE_VERSION);
        //3rd argument to be passed is CursorFactory instance
    }

    /**
     * All CRUD(Create, Read, Update, Delete) Operations
     */

    public void addRegisterFarmerContact(RegisterFarmerContactModel contact) {
        SQLiteDatabase db = this.getWritableDatabase();
        ContentValues values = new ContentValues();
        values.put(KEY_NAME, contact.getContactName()); // Contact Name
        values.put(KEY_TICKET, contact.getTicketNumber());
        values.put(KEY_PH_NO, contact.getPhoneNumber()); // Contact Phone
        values.put(KEY_TIMESTAMP, contact.getTimestamp()); // Contact Phone
        values.put(KEY_IS_SYNC, contact.getIsSync()); // Contact Phone
        values.put(KEY_IS_DELETED, contact.getIsDeleted()); // Contact Phone
        values.put(KEY_TYPE, contact.getType()); // Contact Phone
        try {
            // Inserting Row
            long test = db.insertOrThrow(TABLE_CONTACTS, null, values);
        } catch (Exception e) {
            e.printStackTrace();
        }

        //2nd argument is String containing nullColumnHack
        db.close();
    }    // Upgrading database	@Override

    public boolean checkinDb(RegisterFarmerContactModel precontact) {
        String selectQuery = "SELECT  * FROM " + TABLE_CONTACTS + " WHERE " + KEY_PH_NO + " = " + precontact.getPhoneNumber() + " AND " + KEY_TICKET + " = " + precontact.getTicketNumber();
        List<RegisterFarmerContactModel> contactList = new ArrayList<>();
        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);
        if (cursor.moveToFirst()) {
            do {
                RegisterFarmerContactModel contact = new RegisterFarmerContactModel();
                contact.setAutoid(cursor.getInt(cursor.getColumnIndex(KEY_AUTO_ID)));
                contact.setContactName(cursor.getString(cursor.getColumnIndex(KEY_NAME)));
                contact.setTicketNumber(cursor.getString(cursor.getColumnIndex(KEY_TICKET)));
                contact.setPhoneNumber(cursor.getString(cursor.getColumnIndex(KEY_PH_NO)));
                contact.setTimestamp(cursor.getString(cursor.getColumnIndex(KEY_TIMESTAMP)));
                contact.setIsSync(Integer.parseInt(cursor.getString(cursor.getColumnIndex(KEY_IS_SYNC))));
                contact.setIsDeleted(Integer.parseInt(cursor.getString(cursor.getColumnIndex(KEY_IS_DELETED))));
                contact.setType(cursor.getString(cursor.getColumnIndex(KEY_TYPE)));
                contact.setLast_modified(cursor.getString(cursor.getColumnIndex(KEY_LAST_MODIFIED)));
                contactList.add(contact);
                Log.e("Vinod", "Vinod manual number " + contact.getAutoid() + " | " + contact.getPhoneNumber());
            } while (cursor.moveToNext());
        }
        return contactList.size() > 0;
    }

    // Getting All Contacts
    public List<Contact> getAllContacts() {
        List<Contact> contactList = new ArrayList<Contact>();
        // Select All Query
        //        String selectQuery = "SELECT  * FROM " + TABLE_CONTACTS+" WHERE status = '1' ";
        String selectQuery = "SELECT  * FROM " + TABLE_CONTACTS;

        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);

        // looping through all rows and adding to list
        if (cursor.moveToFirst()) {
            do {
                Contact contact = new Contact();
                contact.setAutoid(cursor.getInt(cursor.getColumnIndex(KEY_AUTO_ID)));
                contact.setName(cursor.getString(cursor.getColumnIndex(KEY_NAME)));
                contact.setPhoneNumber(cursor.getString(cursor.getColumnIndex(KEY_PH_NO)));
                contactList.add(contact);
            } while (cursor.moveToNext());
        }
        return contactList;
    }

    public List<RegisterFarmerContactModel> getAllFarmerRegisterContacts() {
        List<RegisterFarmerContactModel> contactList = new ArrayList<>();
        String selectQuery = "SELECT  * FROM " + TABLE_CONTACTS;
        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);
        if (cursor.moveToFirst()) {
            do {
                RegisterFarmerContactModel contact = new RegisterFarmerContactModel();
                contact.setAutoid(cursor.getInt(cursor.getColumnIndex(KEY_AUTO_ID)));
                contact.setContactName(cursor.getString(cursor.getColumnIndex(KEY_NAME)));
                contact.setPhoneNumber(cursor.getString(cursor.getColumnIndex(KEY_PH_NO)));
                contact.setTimestamp(cursor.getString(cursor.getColumnIndex(KEY_TIMESTAMP)));
                contact.setIsSync(Integer.parseInt(cursor.getString(cursor.getColumnIndex(KEY_IS_SYNC))));
                contact.setIsDeleted(Integer.parseInt(cursor.getString(cursor.getColumnIndex(KEY_IS_DELETED))));
                contact.setType(cursor.getString(cursor.getColumnIndex(KEY_TYPE)));
                contact.setLast_modified(cursor.getString(cursor.getColumnIndex(KEY_LAST_MODIFIED)));
                contactList.add(contact);
            } while (cursor.moveToNext());
        }
        return contactList;
    }

    public List<RegisterFarmerContactModel> getManualData(String date) {
        List<RegisterFarmerContactModel> contactList = new ArrayList<>();
        String selectQuery = "SELECT  * FROM " + TABLE_CONTACTS + " WHERE " + KEY_TYPE + " ='m' and " + KEY_IS_DELETED + " = 0 and " + KEY_TIMESTAMP + " like '" + date + "%'";
        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);
        if (cursor.moveToFirst()) {
            do {
                RegisterFarmerContactModel contact = new RegisterFarmerContactModel();
                contact.setAutoid(cursor.getInt(cursor.getColumnIndex(KEY_AUTO_ID)));
                contact.setContactName(cursor.getString(cursor.getColumnIndex(KEY_NAME)));
                contact.setTicketNumber(cursor.getString(cursor.getColumnIndex(KEY_TICKET)));
                contact.setPhoneNumber(cursor.getString(cursor.getColumnIndex(KEY_PH_NO)));
                contact.setTimestamp(cursor.getString(cursor.getColumnIndex(KEY_TIMESTAMP)));
                contact.setIsSync(Integer.parseInt(cursor.getString(cursor.getColumnIndex(KEY_IS_SYNC))));
                contact.setIsDeleted(Integer.parseInt(cursor.getString(cursor.getColumnIndex(KEY_IS_DELETED))));
                contact.setType(cursor.getString(cursor.getColumnIndex(KEY_TYPE)));
                contact.setLast_modified(cursor.getString(cursor.getColumnIndex(KEY_LAST_MODIFIED)));
                contactList.add(contact);
                Log.e("Vinod", "Vinod manual number " + contact.getAutoid() + " | " + contact.getPhoneNumber());
            } while (cursor.moveToNext());
        }
        return contactList;
    }

    public List<RegisterFarmerContactModel> getUnsyncedManualData(String date) {
        List<RegisterFarmerContactModel> contactList = new ArrayList<>();
        String selectQuery = "SELECT  * FROM " + TABLE_CONTACTS + " WHERE " + KEY_TYPE + " ='m' and " + KEY_IS_SYNC + " = 0 and " + KEY_TIMESTAMP + " like '" + date + "%'";
        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);
        if (cursor.moveToFirst()) {
            do {
                RegisterFarmerContactModel contact = new RegisterFarmerContactModel();
                contact.setAutoid(cursor.getInt(cursor.getColumnIndex(KEY_AUTO_ID)));
                contact.setContactName(cursor.getString(cursor.getColumnIndex(KEY_NAME)));
                contact.setPhoneNumber(cursor.getString(cursor.getColumnIndex(KEY_PH_NO)));
                contact.setTimestamp(cursor.getString(cursor.getColumnIndex(KEY_TIMESTAMP)));
                contact.setTicketNumber(cursor.getString(cursor.getColumnIndex(KEY_TICKET)));
                contact.setIsSync(Integer.parseInt(cursor.getString(cursor.getColumnIndex(KEY_IS_SYNC))));
                contact.setIsDeleted(Integer.parseInt(cursor.getString(cursor.getColumnIndex(KEY_IS_DELETED))));
                contact.setType(cursor.getString(cursor.getColumnIndex(KEY_TYPE)));
                contact.setLast_modified(cursor.getString(cursor.getColumnIndex(KEY_LAST_MODIFIED)));
                contactList.add(contact);
            } while (cursor.moveToNext());
        }
        return contactList;
    }

    public List<RegisterFarmerContactModel> getUnsyncedCallLogData(String date) {
        List<RegisterFarmerContactModel> contactList = new ArrayList<>();
        String selectQuery = "SELECT  * FROM " + TABLE_CONTACTS + " WHERE " + KEY_TYPE + " ='a' and " + KEY_IS_SYNC + " = 0 and " + KEY_TIMESTAMP + " like '" + date + "%'";
        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);
        if (cursor.moveToFirst()) {
            do {
                RegisterFarmerContactModel contact = new RegisterFarmerContactModel();
                contact.setAutoid(cursor.getInt(cursor.getColumnIndex(KEY_AUTO_ID)));
                contact.setContactName(cursor.getString(cursor.getColumnIndex(KEY_NAME)));
                contact.setPhoneNumber(cursor.getString(cursor.getColumnIndex(KEY_PH_NO)));
                contact.setTimestamp(cursor.getString(cursor.getColumnIndex(KEY_TIMESTAMP)));
                contact.setTicketNumber(cursor.getString(cursor.getColumnIndex(KEY_TICKET)));
                contact.setIsSync(Integer.parseInt(cursor.getString(cursor.getColumnIndex(KEY_IS_SYNC))));
                contact.setIsDeleted(Integer.parseInt(cursor.getString(cursor.getColumnIndex(KEY_IS_DELETED))));
                contact.setType(cursor.getString(cursor.getColumnIndex(KEY_TYPE)));
                contact.setLast_modified(cursor.getString(cursor.getColumnIndex(KEY_LAST_MODIFIED)));
                contactList.add(contact);
            } while (cursor.moveToNext());
        }
        return contactList;
    }

    public List<RegisterFarmerContactModel> getFlaggedData(String date) {
        List<RegisterFarmerContactModel> contactList = new ArrayList<>();
        String selectQuery = "SELECT  * FROM " + TABLE_CONTACTS + " WHERE " + KEY_TYPE + " ='a' and " + KEY_IS_SYNC + " = 0 and " + KEY_IS_DELETED + " = 1  and " + KEY_TIMESTAMP + " like '" + date +
                "%'";
        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);
        if (cursor.moveToFirst()) {
            do {
                RegisterFarmerContactModel contact = new RegisterFarmerContactModel();
                contact.setAutoid(cursor.getInt(cursor.getColumnIndex(KEY_AUTO_ID)));
                contact.setContactName(cursor.getString(cursor.getColumnIndex(KEY_NAME)));
                contact.setPhoneNumber(cursor.getString(cursor.getColumnIndex(KEY_PH_NO)));
                contact.setTimestamp(cursor.getString(cursor.getColumnIndex(KEY_TIMESTAMP)));
                contact.setTicketNumber(cursor.getString(cursor.getColumnIndex(KEY_TICKET)));
                contact.setIsSync(Integer.parseInt(cursor.getString(cursor.getColumnIndex(KEY_IS_SYNC))));
                contact.setIsDeleted(Integer.parseInt(cursor.getString(cursor.getColumnIndex(KEY_IS_DELETED))));
                contact.setType(cursor.getString(cursor.getColumnIndex(KEY_TYPE)));
                contact.setLast_modified(cursor.getString(cursor.getColumnIndex(KEY_LAST_MODIFIED)));
                contactList.add(contact);
            } while (cursor.moveToNext());
        }
        return contactList;
    }

    public List<RegisterFarmerContactModel> getUnFlaggedData(String date) {
        List<RegisterFarmerContactModel> contactList = new ArrayList<>();
        String selectQuery = "SELECT  * FROM " + TABLE_CONTACTS + " WHERE " + KEY_TYPE + " ='a' and " + KEY_IS_SYNC + " = 0 and " + KEY_IS_DELETED + " = 0  and " + KEY_TIMESTAMP + " like '" + date +
                "%'";
        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);
        if (cursor.moveToFirst()) {
            do {
                RegisterFarmerContactModel contact = new RegisterFarmerContactModel();
                contact.setAutoid(cursor.getInt(cursor.getColumnIndex(KEY_AUTO_ID)));
                contact.setContactName(cursor.getString(cursor.getColumnIndex(KEY_NAME)));
                contact.setPhoneNumber(cursor.getString(cursor.getColumnIndex(KEY_PH_NO)));
                contact.setTimestamp(cursor.getString(cursor.getColumnIndex(KEY_TIMESTAMP)));
                contact.setTicketNumber(cursor.getString(cursor.getColumnIndex(KEY_TICKET)));
                contact.setIsSync(Integer.parseInt(cursor.getString(cursor.getColumnIndex(KEY_IS_SYNC))));
                contact.setIsDeleted(Integer.parseInt(cursor.getString(cursor.getColumnIndex(KEY_IS_DELETED))));
                contact.setType(cursor.getString(cursor.getColumnIndex(KEY_TYPE)));
                contact.setLast_modified(cursor.getString(cursor.getColumnIndex(KEY_LAST_MODIFIED)));
                contactList.add(contact);
            } while (cursor.moveToNext());
        }
        return contactList;
    }

    public List<RegisterFarmerContactModel> getAutomaticCallData(String date) {
        List<RegisterFarmerContactModel> contactList = new ArrayList<>();
        String selectQuery = "SELECT  * FROM " + TABLE_CONTACTS + " WHERE " + KEY_TYPE + " ='a'  and " + KEY_TIMESTAMP + " like '" + date + "%'";
        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);
        if (cursor.moveToFirst()) {
            do {
                RegisterFarmerContactModel contact = new RegisterFarmerContactModel();
                contact.setAutoid(cursor.getInt(cursor.getColumnIndex(KEY_AUTO_ID)));
                contact.setContactName(cursor.getString(cursor.getColumnIndex(KEY_NAME)));
                contact.setPhoneNumber(cursor.getString(cursor.getColumnIndex(KEY_PH_NO)));
                contact.setTimestamp(cursor.getString(cursor.getColumnIndex(KEY_TIMESTAMP)));
                contact.setTicketNumber(cursor.getString(cursor.getColumnIndex(KEY_TICKET)));
                contact.setIsSync(Integer.parseInt(cursor.getString(cursor.getColumnIndex(KEY_IS_SYNC))));
                contact.setIsDeleted(Integer.parseInt(cursor.getString(cursor.getColumnIndex(KEY_IS_DELETED))));
                contact.setType(cursor.getString(cursor.getColumnIndex(KEY_TYPE)));
                contact.setLast_modified(cursor.getString(cursor.getColumnIndex(KEY_LAST_MODIFIED)));
                contactList.add(contact);
                Log.e("john", " db getAutomaticCallData " + "no.: " + contact.getPhoneNumber() + " | isdel : " + contact.getIsDeleted() + " date :" + contact.getTimestamp());
            } while (cursor.moveToNext());
        }
        return contactList;
    }

    public ArrayList<RegisterFarmerContactModel> getAllContactsThatNeedToSyncedByServer(String ticketNumber) {
        ArrayList<RegisterFarmerContactModel> contactList = new ArrayList<RegisterFarmerContactModel>();
        String selectQuery = "SELECT  * FROM " + TABLE_CONTACTS + " WHERE " + KEY_IS_SYNC + " = '0' and " + KEY_TICKET + " = " + ticketNumber;

        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);

        // looping through all rows and adding to list
        // looping through all rows and adding to list
        if (cursor.moveToFirst()) {
            do {
                RegisterFarmerContactModel contact = new RegisterFarmerContactModel();
                contact.setAutoid(cursor.getInt(cursor.getColumnIndex(KEY_AUTO_ID)));
                contact.setTicketNumber(cursor.getString(cursor.getColumnIndex(KEY_TICKET)));
                contact.setContactName(cursor.getString(cursor.getColumnIndex(KEY_NAME)));
                contact.setPhoneNumber(cursor.getString(cursor.getColumnIndex(KEY_PH_NO)));
                contact.setIsDeleted(cursor.getInt(cursor.getColumnIndex(KEY_IS_DELETED)));
                contact.setType(cursor.getString(cursor.getColumnIndex(KEY_TYPE)));
                contact.setTimestamp(cursor.getString(cursor.getColumnIndex(KEY_TIMESTAMP)));
                contactList.add(contact);
                Log.e("vinod", "vinod - to be sync contact number " + contact.getPhoneNumber());
            } while (cursor.moveToNext());
        }

        Log.e("vinod", "vinod - total sync data count " + contactList.size());
        // return contact list
        return contactList;
    }

    public ArrayList<RegisterFarmerContactModel> getAllContactsThatNeedToSyncedByServer() {
        ArrayList<RegisterFarmerContactModel> contactList = new ArrayList<RegisterFarmerContactModel>();
        String selectQuery = "SELECT  * FROM " + TABLE_CONTACTS + " WHERE " + KEY_IS_SYNC + " = '0' ";

        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);

        // looping through all rows and adding to list
        // looping through all rows and adding to list
        if (cursor.moveToFirst()) {
            do {
                RegisterFarmerContactModel contact = new RegisterFarmerContactModel();
                contact.setAutoid(cursor.getInt(cursor.getColumnIndex(KEY_AUTO_ID)));
                contact.setTicketNumber(cursor.getString(cursor.getColumnIndex(KEY_TICKET)));
                contact.setContactName(cursor.getString(cursor.getColumnIndex(KEY_NAME)));
                contact.setPhoneNumber(cursor.getString(cursor.getColumnIndex(KEY_PH_NO)));
                contact.setIsDeleted(cursor.getInt(cursor.getColumnIndex(KEY_IS_DELETED)));
                contact.setType(cursor.getString(cursor.getColumnIndex(KEY_TYPE)));
                contact.setTimestamp(cursor.getString(cursor.getColumnIndex(KEY_TIMESTAMP)));
                contactList.add(contact);
                Log.e("vinod", "vinod - to be sync contact number " + contact.getPhoneNumber());
            } while (cursor.moveToNext());
        }

        Log.e("vinod", "vinod - total sync data count " + contactList.size());
        // return contact list
        return contactList;
    }

    public Integer deleteSingleContact(Integer id) {
        SQLiteDatabase db = this.getWritableDatabase();
        return db.delete("contacts",
                "_id = ? ",
                new String[]{Integer.toString(id)});
    }

    public void deleteAll() {
       /* SQLiteDatabase db = this.getWritableDatabase();
        // db.delete(TABLE_NAME,null,null);
        //db.execSQL("delete * from"+ TABLE_NAME);
        db.execSQL("delete from " + TABLE_CONTACTS +" where "+ KEY_TIMESTAMP + " NOT LIKE '"+ date+"%'");
        db.close();*/
    }

    public void deleteManualContact(RegisterFarmerContactModel model) {
        SQLiteDatabase db = this.getWritableDatabase();
        String query = "DELETE FROM " + TABLE_CONTACTS + " WHERE " + KEY_AUTO_ID + " = " + model.getAutoid() + "";
        db.execSQL(query);
        System.out.println("Vinod deleted auto id: " + model.getAutoid());
        String selectQuery = "SELECT  * FROM " + TABLE_CONTACTS;
        Cursor cursor = db.rawQuery(selectQuery, null);
        if (cursor.moveToFirst()) {
            do {
                System.out.println("Vinod id: " + cursor.getString(cursor.getColumnIndex(KEY_AUTO_ID)) + " | NO: " + cursor.getString(cursor.getColumnIndex(KEY_PH_NO)));
            } while (cursor.moveToNext());
        }

        db.close();
    }

    public int updateRegisterContact(RegisterFarmerContactModel contact) {
        SQLiteDatabase db = this.getWritableDatabase();
        ContentValues values = new ContentValues();
        values.put(KEY_IS_SYNC, contact.getIsSync());
        values.put(KEY_IS_DELETED, contact.getIsDeleted());
        String[] id = new String[]{Integer.toString(contact.getAutoid())};
        int test = db.update(TABLE_CONTACTS, values, KEY_AUTO_ID + " = ? ", id);

        return test;
    }

    public int updateDeleteStatusOfContact(RegisterFarmerContactModel contact) {
        SQLiteDatabase db = this.getWritableDatabase();
        ContentValues values = new ContentValues();
        values.put(KEY_IS_DELETED, contact.getIsDeleted());
        values.put(KEY_IS_SYNC, contact.getIsSync());
        String[] id = new String[]{Integer.toString(contact.getAutoid())};
        int test = db.update(TABLE_CONTACTS, values, KEY_AUTO_ID + " = ? ", id);
        return test;
    }

    public int updateSyncStatus() {
        SQLiteDatabase db = this.getWritableDatabase();
        ContentValues values = new ContentValues();
        values.put(KEY_IS_SYNC, 1);
        int test = db.update(TABLE_CONTACTS, values, null, null);
        return test;
    }

    public void onCreate(SQLiteDatabase db) {
        String CREATE_CONTACTS_TABLE = "CREATE TABLE IF NOT EXISTS "
                + TABLE_CONTACTS + "("
                + KEY_AUTO_ID + " INTEGER PRIMARY KEY AUTOINCREMENT, "
                + KEY_TICKET + " TEXT ,"
                + KEY_NAME + " TEXT ,"
                + KEY_PH_NO + " TEXT ,"
                + KEY_TIMESTAMP + " DATETIME ,"
                + KEY_IS_SYNC + " INTEGER DEFAULT 0,"
                + KEY_IS_DELETED + " INTEGER DEFAULT 0,"
                + KEY_TYPE + " TEXT ,"
                + KEY_LAST_MODIFIED + " DATETIME DEFAULT  CURRENT_TIMESTAMP)";
        db.execSQL(CREATE_CONTACTS_TABLE);
    }

    @Override
    public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
        db.execSQL("DROP TABLE IF EXISTS " + TABLE_CONTACTS);
        onCreate(db);
    }


}
