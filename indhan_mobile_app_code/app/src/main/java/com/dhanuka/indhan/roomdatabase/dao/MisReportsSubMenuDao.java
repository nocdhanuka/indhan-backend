package com.dhanuka.indhan.roomdatabase.dao;

import androidx.room.Dao;
import androidx.room.Insert;
import androidx.room.OnConflictStrategy;
import androidx.room.Query;

import com.dhanuka.indhan.Activity.misAndReports.model.MisSubMenu;

import java.util.List;

@Dao
public interface MisReportsSubMenuDao {

    @Insert
    void insert(List<MisSubMenu> data);

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    void insert(MisSubMenu data);

    @Query("SELECT * FROM MisSubMenu")
    List<MisSubMenu> getAllMenuList();

    @Query("SELECT COUNT(report_type_id) FROM MisSubMenu")
    int getNumberOfRows();

    @Query("DELETE FROM MisSubMenu")
    void deleteTableData();
}