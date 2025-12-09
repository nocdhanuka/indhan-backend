package com.dhanuka.indhan.DataBase.DbStructure;

import android.content.ContentProvider;
import android.content.ContentValues;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteQueryBuilder;
import android.net.Uri;

import com.dhanuka.indhan.DataBase.TableStructureModels.BDOPlanS1DetailTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.BDOPlanS1Table;
import com.dhanuka.indhan.DataBase.TableStructureModels.CropTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.DataSourceTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.DistrictTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.ImageOfflineTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.LanguageTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.MenuTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.MySyllabusForBDOTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.PlanAtpTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.ProductCropTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.ProductTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.StateTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.SyllabusTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.TehsilTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.UserPermissionTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.UserTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.UserTeamTable;
import com.dhanuka.indhan.Preference.Preference;


/**
 * Created by John on 5/3/2018.
 */

public class DatabaseProvider extends ContentProvider {
    private static final String UNKNOWN_URI = "Unknown URI ";
    private DBHelper dbHelper;
    private Preference preference;

    @Override
    public boolean onCreate() {
        preference = new Preference(getContext());
        String s = preference.getString("user_type");
        dbHelper = new DBHelper(getContext());
        dbHelper.getWritableDatabase();
        return true;
    }


    @Override
    public Cursor query(Uri uri, String[] projection, String selection, String[] selectionArgs, String sortOrder) {
        if (dbHelper == null) {
            dbHelper = new DBHelper(getContext());
        }
        SQLiteDatabase db = dbHelper.getReadableDatabase();
        final int token = ContentDescriptor.URI_MATCHER.match(uri);
        Cursor result = null;
        switch (token) {
            case UserTable.PATH_TOKEN: {
                result = doQuery(db, uri, UserTable.TABLE_NAME, projection, selection, selectionArgs, sortOrder);
                break;
            }
            case UserTeamTable.PATH_TOKEN: {
                result = doQuery(db, uri, UserTeamTable.TABLE_NAME, projection, selection, selectionArgs, sortOrder);
                break;
            }
            case CropTable.PATH_TOKEN: {
                result = doQuery(db, uri, CropTable.TABLE_NAME, projection, selection, selectionArgs, sortOrder);
                break;
            }
            case ProductTable.PATH_TOKEN: {
                result = doQuery(db, uri, ProductTable.TABLE_NAME, projection, selection, selectionArgs, sortOrder);
                break;
            }
            case MenuTable.PATH_TOKEN: {
                result = doQuery(db, uri, MenuTable.TABLE_NAME, projection, selection, selectionArgs, sortOrder);
                break;
            }
            case UserPermissionTable.PATH_TOKEN: {
                result = doQuery(db, uri, UserPermissionTable.TABLE_NAME, projection, selection, selectionArgs, sortOrder);
                break;
            }
            case SyllabusTable.PATH_TOKEN: {
                result = doQuery(db, uri, SyllabusTable.TABLE_NAME, projection, selection, selectionArgs, sortOrder);
                break;
            }
            case DataSourceTable.PATH_TOKEN: {
                result = doQuery(db, uri, DataSourceTable.TABLE_NAME, projection, selection, selectionArgs, sortOrder);
                break;
            }
            case DistrictTable.PATH_TOKEN: {
                result = doQuery(db, uri, DistrictTable.TABLE_NAME, projection, selection, selectionArgs, sortOrder);
                break;
            }
            case MySyllabusForBDOTable.PATH_TOKEN: {
                result = doQuery(db, uri, MySyllabusForBDOTable.TABLE_NAME, projection, selection, selectionArgs, sortOrder);
                break;
            }
            case PlanAtpTable.PATH_TOKEN: {
                result = doQuery(db, uri, PlanAtpTable.TABLE_NAME, projection, selection, selectionArgs, sortOrder);
                break;
            }
            case StateTable.PATH_TOKEN: {
                result = doQuery(db, uri, StateTable.TABLE_NAME, projection, selection, selectionArgs, sortOrder);
                break;
            }
            case TehsilTable.PATH_TOKEN: {
                result = doQuery(db, uri, TehsilTable.TABLE_NAME, projection, selection, selectionArgs, sortOrder);
                break;
            }
            case BDOPlanS1Table.PATH_TOKEN: {
                result = doQuery(db, uri, BDOPlanS1Table.TABLE_NAME, projection, selection, selectionArgs, sortOrder);
                break;
            }
            case BDOPlanS1DetailTable.PATH_TOKEN: {
                result = doQuery(db, uri, BDOPlanS1DetailTable.TABLE_NAME, projection, selection, selectionArgs, sortOrder);
                break;
            }
            case ImageOfflineTable.PATH_TOKEN: {
                result = doQuery(db, uri, ImageOfflineTable.TABLE_NAME, projection, selection, selectionArgs, sortOrder);
                break;
            }
            case ProductCropTable.PATH_TOKEN: {
                result = doQuery(db, uri, ProductCropTable.TABLE_NAME, projection, selection, selectionArgs, sortOrder);
                break;
            }
            case LanguageTable.PATH_TOKEN: {
                result = doQuery(db, uri, LanguageTable.TABLE_NAME, projection, selection, selectionArgs, sortOrder);
                break;
            }
        }
        return result;
    }

    @Override
    public String getType(Uri uri) {
        return null;
    }

    @Override
    public Uri insert(Uri uri, ContentValues values) {
        if (dbHelper == null) {
            dbHelper = new DBHelper(getContext());
        }
        dbHelper.getWritableDatabase();
        SQLiteDatabase db = dbHelper.getWritableDatabase();
        int token = ContentDescriptor.URI_MATCHER.match(uri);
        Uri result = null;
        switch (token) {
            case UserTable.PATH_TOKEN: {
                result = doInsert(db, UserTable.TABLE_NAME, UserTable.CONTENT_URI, uri, values);
                break;
            }
            case UserTeamTable.PATH_TOKEN: {
                result = doInsert(db, UserTeamTable.TABLE_NAME, UserTeamTable.CONTENT_URI, uri, values);
                break;
            }
            case MenuTable.PATH_TOKEN: {
                result = doInsert(db, MenuTable.TABLE_NAME, MenuTable.CONTENT_URI, uri, values);
                break;
            }
            case CropTable.PATH_TOKEN: {
                result = doInsert(db, CropTable.TABLE_NAME, CropTable.CONTENT_URI, uri, values);
                break;
            }
            case ProductTable.PATH_TOKEN: {
                result = doInsert(db, ProductTable.TABLE_NAME, ProductTable.CONTENT_URI, uri, values);
                break;
            }
            case UserPermissionTable.PATH_TOKEN: {
                result = doInsert(db, UserPermissionTable.TABLE_NAME, UserPermissionTable.CONTENT_URI, uri, values);
                break;
            }
            case SyllabusTable.PATH_TOKEN: {
                result = doInsert(db, SyllabusTable.TABLE_NAME, SyllabusTable.CONTENT_URI, uri, values);
                break;
            }
            case DataSourceTable.PATH_TOKEN: {
                result = doInsert(db, DataSourceTable.TABLE_NAME, DataSourceTable.CONTENT_URI, uri, values);
                break;
            }
            case DistrictTable.PATH_TOKEN: {
                result = doInsert(db, DistrictTable.TABLE_NAME, DistrictTable.CONTENT_URI, uri, values);
                break;
            }
            case MySyllabusForBDOTable.PATH_TOKEN: {
                result = doInsert(db, MySyllabusForBDOTable.TABLE_NAME, MySyllabusForBDOTable.CONTENT_URI, uri, values);
                break;
            }
            case PlanAtpTable.PATH_TOKEN: {
                result = doInsert(db, PlanAtpTable.TABLE_NAME, PlanAtpTable.CONTENT_URI, uri, values);
                break;
            }
            case StateTable.PATH_TOKEN: {
                result = doInsert(db, StateTable.TABLE_NAME, StateTable.CONTENT_URI, uri, values);
                break;
            }
            case TehsilTable.PATH_TOKEN: {
                result = doInsert(db, TehsilTable.TABLE_NAME, TehsilTable.CONTENT_URI, uri, values);
                break;
            }
            case BDOPlanS1DetailTable.PATH_TOKEN: {
                result = doInsert(db, BDOPlanS1DetailTable.TABLE_NAME, BDOPlanS1DetailTable.CONTENT_URI, uri, values);
                break;
            }
            case BDOPlanS1Table.PATH_TOKEN: {
                result = doInsert(db, BDOPlanS1Table.TABLE_NAME, BDOPlanS1Table.CONTENT_URI, uri, values);
                break;
            }
            case ImageOfflineTable.PATH_TOKEN: {
                result = doInsert(db, ImageOfflineTable.TABLE_NAME, ImageOfflineTable.CONTENT_URI, uri, values);
                break;
            }
            case ProductCropTable.PATH_TOKEN: {
                result = doInsert(db, ProductCropTable.TABLE_NAME, ProductCropTable.CONTENT_URI, uri, values);
                break;
            }
            case LanguageTable.PATH_TOKEN: {
                result = doInsert(db, LanguageTable.TABLE_NAME, LanguageTable.CONTENT_URI, uri, values);
                break;
            }
        }
        if (result == null) {
            throw new IllegalArgumentException(UNKNOWN_URI + uri);
        }
        return result;
    }

    @Override
    public int bulkInsert(Uri uri, ContentValues[] values) {
        String table = null;
        int token = ContentDescriptor.URI_MATCHER.match(uri);
        switch (token) {
            case UserTable.PATH_TOKEN: {
                table = UserTable.TABLE_NAME;
                break;
            }
            case UserTeamTable.PATH_TOKEN: {
                table = UserTeamTable.TABLE_NAME;
                break;
            }
            case MenuTable.PATH_TOKEN: {
                table = MenuTable.TABLE_NAME;
                break;
            }
            case ProductTable.PATH_TOKEN: {
                table = ProductTable.TABLE_NAME;
                break;
            }
            case CropTable.PATH_TOKEN: {
                table = CropTable.TABLE_NAME;
                break;
            }
            case UserPermissionTable.PATH_TOKEN: {
                table = UserPermissionTable.TABLE_NAME;
                break;
            }
            case SyllabusTable.PATH_TOKEN: {
                table = SyllabusTable.TABLE_NAME;
                break;
            }
            case StateTable.PATH_TOKEN: {
                table = StateTable.TABLE_NAME;
                break;
            }
            case LanguageTable.PATH_TOKEN: {
                table = LanguageTable.TABLE_NAME;
                break;
            }
            case DistrictTable.PATH_TOKEN: {
                table = DistrictTable.TABLE_NAME;
                break;
            }
            case DataSourceTable.PATH_TOKEN: {
                table = DataSourceTable.TABLE_NAME;
                break;
            }
            case MySyllabusForBDOTable.PATH_TOKEN: {
                table = MySyllabusForBDOTable.TABLE_NAME;
                break;
            }
            case PlanAtpTable.PATH_TOKEN: {
                table = PlanAtpTable.TABLE_NAME;
                break;
            }
            case TehsilTable.PATH_TOKEN: {
                table = TehsilTable.TABLE_NAME;
                break;
            }
            case BDOPlanS1DetailTable.PATH_TOKEN: {
                table = BDOPlanS1DetailTable.TABLE_NAME;
                break;
            }
            case BDOPlanS1Table.PATH_TOKEN: {
                table = BDOPlanS1Table.TABLE_NAME;
                break;
            }
            case ImageOfflineTable.PATH_TOKEN: {
                table = ImageOfflineTable.TABLE_NAME;
                break;
            }
            case ProductCropTable.PATH_TOKEN: {
                table = ProductCropTable.TABLE_NAME;
                break;
            }
        }
        SQLiteDatabase db = dbHelper.getWritableDatabase();
        db.beginTransaction();
        for (ContentValues cv : values) {
            db.insert(table, null, cv);
        }
        db.setTransactionSuccessful();
        db.endTransaction();
        getContext().getContentResolver().notifyChange(uri, null);
        return values.length;
    }

    @Override
    public int delete(Uri uri, String selection, String[] selectionArgs) {
        if (dbHelper == null) {
            dbHelper = new DBHelper(getContext());
        }
        SQLiteDatabase db = dbHelper.getWritableDatabase();
        int token = ContentDescriptor.URI_MATCHER.match(uri);
        int result = 0;
        switch (token) {
            case UserTable.PATH_TOKEN: {
                result = doDelete(db, uri, UserTable.TABLE_NAME, selection, selectionArgs);
                break;
            }
            case LanguageTable.PATH_TOKEN: {
                result = doDelete(db, uri, LanguageTable.TABLE_NAME, selection, selectionArgs);
                break;
            }
            case UserTeamTable.PATH_TOKEN: {
                result = doDelete(db, uri, UserTeamTable.TABLE_NAME, selection, selectionArgs);
                break;
            }
            case ProductTable.PATH_TOKEN: {
                result = doDelete(db, uri, ProductTable.TABLE_NAME, selection, selectionArgs);
                break;
            }
            case MenuTable.PATH_TOKEN: {
                result = doDelete(db, uri, MenuTable.TABLE_NAME, selection, selectionArgs);
                break;
            }
            case CropTable.PATH_TOKEN: {
                result = doDelete(db, uri, CropTable.TABLE_NAME, selection, selectionArgs);
                break;
            }
            case UserPermissionTable.PATH_TOKEN: {
                result = doDelete(db, uri, UserPermissionTable.TABLE_NAME, selection, selectionArgs);
                break;
            }
            case SyllabusTable.PATH_TOKEN: {
                result = doDelete(db, uri, SyllabusTable.TABLE_NAME, selection, selectionArgs);
                break;
            }
            case StateTable.PATH_TOKEN: {
                result = doDelete(db, uri, StateTable.TABLE_NAME, selection, selectionArgs);
                break;
            }
            case DistrictTable.PATH_TOKEN: {
                result = doDelete(db, uri, DistrictTable.TABLE_NAME, selection, selectionArgs);
                break;
            }
            case DataSourceTable.PATH_TOKEN: {
                result = doDelete(db, uri, DataSourceTable.TABLE_NAME, selection, selectionArgs);
                break;
            }
            case PlanAtpTable.PATH_TOKEN: {
                result = doDelete(db, uri, PlanAtpTable.TABLE_NAME, selection, selectionArgs);
                break;
            }
            case MySyllabusForBDOTable.PATH_TOKEN: {
                result = doDelete(db, uri, MySyllabusForBDOTable.TABLE_NAME, selection, selectionArgs);
                break;
            }
            case TehsilTable.PATH_TOKEN: {
                result = doDelete(db, uri, TehsilTable.TABLE_NAME, selection, selectionArgs);
                break;
            }
            case BDOPlanS1DetailTable.PATH_TOKEN: {
                result = doDelete(db, uri, BDOPlanS1DetailTable.TABLE_NAME, selection, selectionArgs);
                break;
            }
            case BDOPlanS1Table.PATH_TOKEN: {
                result = doDelete(db, uri, BDOPlanS1Table.TABLE_NAME, selection, selectionArgs);
                break;
            }
            case ImageOfflineTable.PATH_TOKEN: {
                result = doDelete(db, uri, ImageOfflineTable.TABLE_NAME, selection, selectionArgs);
                break;
            }
            case ProductCropTable.PATH_TOKEN: {
                result = doDelete(db, uri, ProductCropTable.TABLE_NAME, selection, selectionArgs);
                break;
            }
        }
        return result;
    }

    @Override
    public int update(Uri uri, ContentValues values, String selection, String[] selectionArgs) {
        if (dbHelper == null) {
            dbHelper = new DBHelper(getContext());
        }
        SQLiteDatabase db = dbHelper.getWritableDatabase();
        int token = ContentDescriptor.URI_MATCHER.match(uri);
        int result = 0;
        switch (token) {
            case UserTable.PATH_TOKEN: {
                result = doUpdate(db, uri, UserTable.TABLE_NAME, selection, selectionArgs, values);
                break;
            }
            case LanguageTable.PATH_TOKEN: {
                result = doUpdate(db, uri, LanguageTable.TABLE_NAME, selection, selectionArgs, values);
                break;
            }
            case UserTeamTable.PATH_TOKEN: {
                result = doUpdate(db, uri, UserTeamTable.TABLE_NAME, selection, selectionArgs, values);
                break;
            }
            case CropTable.PATH_TOKEN: {
                result = doUpdate(db, uri, CropTable.TABLE_NAME, selection, selectionArgs, values);
                break;
            }
            case ProductTable.PATH_TOKEN: {
                result = doUpdate(db, uri, ProductTable.TABLE_NAME, selection, selectionArgs, values);
                break;
            }
            case MenuTable.PATH_TOKEN: {
                result = doUpdate(db, uri, MenuTable.TABLE_NAME, selection, selectionArgs, values);
                break;
            }
            case UserPermissionTable.PATH_TOKEN: {
                result = doUpdate(db, uri, ProductTable.TABLE_NAME, selection, selectionArgs, values);
                break;
            }
            case SyllabusTable.PATH_TOKEN: {
                result = doUpdate(db, uri, SyllabusTable.TABLE_NAME, selection, selectionArgs, values);
                break;
            }
            case StateTable.PATH_TOKEN: {
                result = doUpdate(db, uri, StateTable.TABLE_NAME, selection, selectionArgs, values);
                break;
            }
            case DistrictTable.PATH_TOKEN: {
                result = doUpdate(db, uri, DistrictTable.TABLE_NAME, selection, selectionArgs, values);
                break;
            }
            case PlanAtpTable.PATH_TOKEN: {
                result = doUpdate(db, uri, PlanAtpTable.TABLE_NAME, selection, selectionArgs, values);
                break;
            }
            case MySyllabusForBDOTable.PATH_TOKEN: {
                result = doUpdate(db, uri, MySyllabusForBDOTable.TABLE_NAME, selection, selectionArgs, values);
                break;
            }
            case DataSourceTable.PATH_TOKEN: {
                result = doUpdate(db, uri, DataSourceTable.TABLE_NAME, selection, selectionArgs, values);
                break;
            }
            case TehsilTable.PATH_TOKEN: {
                result = doUpdate(db, uri, TehsilTable.TABLE_NAME, selection, selectionArgs, values);
                break;
            }
            case BDOPlanS1DetailTable.PATH_TOKEN: {
                result = doUpdate(db, uri, BDOPlanS1DetailTable.TABLE_NAME, selection, selectionArgs, values);
                break;
            }
            case BDOPlanS1Table.PATH_TOKEN: {
                result = doUpdate(db, uri, BDOPlanS1Table.TABLE_NAME, selection, selectionArgs, values);
                break;
            }
            case ImageOfflineTable.PATH_TOKEN: {
                result = doUpdate(db, uri, ImageOfflineTable.TABLE_NAME, selection, selectionArgs, values);
                break;
            }
            case ProductCropTable.PATH_TOKEN: {
                result = doUpdate(db, uri, ProductCropTable.TABLE_NAME, selection, selectionArgs, values);
                break;
            }
        }
        return result;
    }

    public void closeDatabase() {
        SQLiteDatabase db = dbHelper.getReadableDatabase();
        db.close();
    }

    private Cursor doQuery(SQLiteDatabase db, Uri uri, String tableName, String[] projection, String selection, String[] selectionArgs, String sortOrder) {
        SQLiteQueryBuilder builder = new SQLiteQueryBuilder();
        builder.setTables(tableName);
        Cursor result = builder.query(db, projection, selection, selectionArgs, sortOrder, null, null);
        result.setNotificationUri(getContext().getContentResolver(), uri);
        return result;
    }

    private int doUpdate(SQLiteDatabase db, Uri uri, String tableName, String selection, String[] selectionArgs, ContentValues values) {
        int result = db.update(tableName, values, selection, selectionArgs);
        getContext().getContentResolver().notifyChange(uri, null);
        return result;
    }

    private int doDelete(SQLiteDatabase db, Uri uri, String tableName, String selection, String[] selectionArgs) {
        int result = db.delete(tableName, selection, selectionArgs);
        getContext().getContentResolver().notifyChange(uri, null);
        return result;
    }

    private Uri doInsert(SQLiteDatabase db, String tableName, Uri contentUri, Uri uri, ContentValues values) {
        long id = 0;
        try {
            id = db.insertOrThrow(tableName, null, values);
        } catch (Exception e) {
            e.printStackTrace();
            return null;
        }
        Uri result = contentUri.buildUpon().appendPath(String.valueOf(id)).build();
        getContext().getContentResolver().notifyChange(uri, null);
        return result;
    }
}