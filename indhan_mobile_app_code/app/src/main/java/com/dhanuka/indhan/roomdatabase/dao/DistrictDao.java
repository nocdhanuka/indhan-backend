package com.dhanuka.indhan.roomdatabase.dao;

import androidx.room.Dao;
import androidx.room.Insert;
import androidx.room.OnConflictStrategy;
import androidx.room.Query;

import com.dhanuka.indhan.MenuScreenActivity.dbmodel.AllDistrictBean;

import java.util.List;

@Dao
public interface DistrictDao {

    @Insert
    void insert(List<AllDistrictBean.DistrictData> data);


    @Insert(onConflict = OnConflictStrategy.REPLACE)
    void insert(AllDistrictBean.DistrictData data);

    @Query("SELECT * FROM DistrictData WHERE state_id =:stateId")
    List<AllDistrictBean.DistrictData> getAllDistrictList(int stateId);

    @Query("SELECT * FROM DistrictData")
    List<AllDistrictBean.DistrictData> getAllDistrictListAll();

    @Query("SELECT COUNT(district_id) FROM DistrictData")
    int getNumberOfRows();


    @Query("SELECT * FROM DistrictData WHERE district =:districtName")
    AllDistrictBean.DistrictData getDistrictIdFromName(String districtName);

    @Query("DELETE FROM DistrictData")
    void deleteDistrictList();
}