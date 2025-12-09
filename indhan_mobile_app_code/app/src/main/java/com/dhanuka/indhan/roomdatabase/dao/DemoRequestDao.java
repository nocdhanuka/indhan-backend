package com.dhanuka.indhan.roomdatabase.dao;

import androidx.room.Dao;
import androidx.room.Insert;
import androidx.room.OnConflictStrategy;
import androidx.room.Query;

import com.dhanuka.indhan.DemoRequestActivity.models.DemoRequestOfflineModel;

import java.util.List;

@Dao
public interface DemoRequestDao {

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    void insert(DemoRequestOfflineModel data);

    @Query("SELECT * FROM DemoRequestOfflineModel")
    List<DemoRequestOfflineModel> getAllDemoRequestList();

    @Query("DELETE FROM DemoRequestOfflineModel WHERE _id =:id")
    void deleteTableData(int id);

    @Query("DELETE FROM DemoRequestOfflineModel")
    void deleteTableData();
}