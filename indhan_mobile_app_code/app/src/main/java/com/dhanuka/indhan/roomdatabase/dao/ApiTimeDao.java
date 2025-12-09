package com.dhanuka.indhan.roomdatabase.dao;

import androidx.room.Dao;
import androidx.room.Insert;
import androidx.room.Query;

import com.dhanuka.indhan.roomdatabase.model.ApiTimeTableModel;

@Dao
public interface ApiTimeDao {

    @Insert
    long insert(ApiTimeTableModel data);

    @Query("DELETE FROM ApiTimeTable")
    void deleteTableData();

    @Query("SELECT * FROM ApiTimeTable WHERE apiName =:apiName")
    ApiTimeTableModel getApiTimeData(String apiName);

    @Query("SELECT * FROM ApiTimeTable WHERE apiName =:apiName AND typeId =:type")
    ApiTimeTableModel getApiTimeData(String apiName, String type);

    @Query("UPDATE ApiTimeTable SET updatedAt =:updatedDate WHERE apiName =:apiName")
    void updateTimeInDb(String apiName, String updatedDate);

    @Query("UPDATE ApiTimeTable SET updatedAt =:updatedDate WHERE apiName =:apiName AND typeId =:type")
    void updateTimeInDb(String apiName, String updatedDate, String type);
}
