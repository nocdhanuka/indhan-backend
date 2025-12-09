package com.dhanuka.indhan.DataBase.DbStructure;

/**
 * Created by John on 5/3/2018.
 */

import android.content.UriMatcher;
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
import com.dhanuka.indhan.DataBase.TableStructureModels.UserTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.UserTeamTable;


public class ContentDescriptor {
    private static final String AUTHORITY = "com.dhanuka.indhan";
    public static final Uri BASE_URI = Uri.parse("content://" + AUTHORITY);
    public static final UriMatcher URI_MATCHER = buildUriMatcher();

    private static UriMatcher buildUriMatcher() {
        final UriMatcher matcher = new UriMatcher(UriMatcher.NO_MATCH);
        matcher.addURI(AUTHORITY, UserTable.PATH, UserTable.PATH_TOKEN);
        matcher.addURI(AUTHORITY, UserTeamTable.PATH, UserTeamTable.PATH_TOKEN);
        matcher.addURI(AUTHORITY, MenuTable.PATH, MenuTable.PATH_TOKEN);
        matcher.addURI(AUTHORITY, CropTable.PATH, CropTable.PATH_TOKEN);
        matcher.addURI(AUTHORITY, ProductTable.PATH, ProductTable.PATH_TOKEN);
        matcher.addURI(AUTHORITY, SyllabusTable.PATH, SyllabusTable.PATH_TOKEN);
        matcher.addURI(AUTHORITY, DataSourceTable.PATH, DataSourceTable.PATH_TOKEN);
        matcher.addURI(AUTHORITY, StateTable.PATH, StateTable.PATH_TOKEN);
        matcher.addURI(AUTHORITY, PlanAtpTable.PATH, PlanAtpTable.PATH_TOKEN);
        matcher.addURI(AUTHORITY, DistrictTable.PATH, DistrictTable.PATH_TOKEN);
        matcher.addURI(AUTHORITY, TehsilTable.PATH, TehsilTable.PATH_TOKEN);
        matcher.addURI(AUTHORITY, MySyllabusForBDOTable.PATH, MySyllabusForBDOTable.PATH_TOKEN);
        matcher.addURI(AUTHORITY, BDOPlanS1DetailTable.PATH, BDOPlanS1DetailTable.PATH_TOKEN);
        matcher.addURI(AUTHORITY, BDOPlanS1Table.PATH, BDOPlanS1Table.PATH_TOKEN);
        matcher.addURI(AUTHORITY, ImageOfflineTable.PATH, ImageOfflineTable.PATH_TOKEN);
        matcher.addURI(AUTHORITY, ProductCropTable.PATH, ProductCropTable.PATH_TOKEN);
        matcher.addURI(AUTHORITY, LanguageTable.PATH, LanguageTable.PATH_TOKEN);
        return matcher;
    }
}