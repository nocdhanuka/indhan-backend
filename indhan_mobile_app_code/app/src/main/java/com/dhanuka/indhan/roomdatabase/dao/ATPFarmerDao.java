package com.dhanuka.indhan.roomdatabase.dao;

import androidx.room.Dao;
import androidx.room.Insert;
import androidx.room.Query;

import com.dhanuka.indhan.ATPMenu.activity.models.ValidatedfarmerListResponse;

import java.util.List;

@Dao
public interface ATPFarmerDao {

    @Insert
    long insert(ValidatedfarmerListResponse.Data data);

    @Query("SELECT * FROM ATPFarmer WHERE planAtpId =:atpId")
    List<ValidatedfarmerListResponse.Data> getAllFarmerList(int atpId);

    @Query("DELETE FROM ATPFarmer")
    void deleteTableData();


    @Query("SELECT * FROM ATPFarmer WHERE isNewNumber =:value")
    List<ValidatedfarmerListResponse.Data> getAllOfflineFarmerList(boolean value);


    @Query("UPDATE ATPFarmer SET isNewNumber =:value WHERE planAtpId =:atpId")
    void updateStatus(String atpId, boolean value);
}