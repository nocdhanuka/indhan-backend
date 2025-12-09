package com.dhanuka.indhan.roomdatabase.dao;

import androidx.room.Dao;
import androidx.room.Insert;
import androidx.room.Query;

import com.dhanuka.indhan.MenuScreenActivity.dbmodel.DemoRequestList;

import java.util.List;

@Dao
public interface DemoRequestListDao {

    @Insert
    void insert(List<DemoRequestList> data);

    @Query("SELECT * FROM DemoRequestList")
    List<DemoRequestList> getAllDemoRequestList();

    @Query("SELECT COUNT(dd_id) FROM DemoRequestList")
    int getNumberOfRows();


    @Query("DELETE  FROM DemoRequestList")
    void deleteTableData();
}