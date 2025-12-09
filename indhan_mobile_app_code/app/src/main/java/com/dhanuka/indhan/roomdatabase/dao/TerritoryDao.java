package com.dhanuka.indhan.roomdatabase.dao;

import androidx.room.Dao;
import androidx.room.Insert;
import androidx.room.OnConflictStrategy;
import androidx.room.Query;

import com.dhanuka.indhan.Model.TerritoryResponse;

import java.util.List;

@Dao
public interface TerritoryDao {

    @Insert
    void insert(List<TerritoryResponse.TerritoryData> data);

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    void insert(TerritoryResponse.TerritoryData data);

    @Query("SELECT * FROM TerritoryData WHERE district_id =:districtId")
    List<TerritoryResponse.TerritoryData> getAllTerritoryList(int districtId);

    @Query("SELECT * FROM TerritoryData WHERE district_id =:districtId")
    TerritoryResponse.TerritoryData getTerritoryData(int districtId);

    @Query("SELECT COUNT(territory_id) FROM TerritoryData")
    int getNumberOfRows();

    @Query("DELETE FROM TerritoryData")
    void deleteDistrictList();
}