package com.dhanuka.indhan.roomdatabase.dao;

import androidx.room.Dao;
import androidx.room.Insert;
import androidx.room.OnConflictStrategy;
import androidx.room.Query;

import com.dhanuka.indhan.MenuScreenActivity.dbmodel.AllTehsilBean;

import java.util.List;

@Dao
public interface TehsilDao {

    @Insert
    void insert(List<AllTehsilBean.TehsilData> data);


    @Insert(onConflict = OnConflictStrategy.REPLACE)
    void insert(AllTehsilBean.TehsilData data);

    @Query("SELECT * FROM TehsilData WHERE district_id =:districtId")
    List<AllTehsilBean.TehsilData> getAllTehsilList(int districtId);

    @Query("SELECT COUNT(tehsil_id) FROM TehsilData")
    int getNumberOfRows();


    @Query("SELECT * FROM TehsilData WHERE tehsil =:teshilName")
    AllTehsilBean.TehsilData getTehsilIdFromData(String teshilName);

    @Query("DELETE FROM TehsilData")
    void deleteTehsilList();
}