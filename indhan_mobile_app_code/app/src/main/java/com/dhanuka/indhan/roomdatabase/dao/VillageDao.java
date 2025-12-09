package com.dhanuka.indhan.roomdatabase.dao;

import androidx.room.Dao;
import androidx.room.Insert;
import androidx.room.OnConflictStrategy;
import androidx.room.Query;

import com.dhanuka.indhan.MenuScreenActivity.dbmodel.AllVillageBean;

import java.util.List;

@Dao
public interface VillageDao {

    @Insert
    void insert(List<AllVillageBean.VillageData> data);

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    void insert(AllVillageBean.VillageData data);

    @Query("SELECT * FROM VillageData WHERE tehsil_id =:tehsilId")
    List<AllVillageBean.VillageData> getAllVillageList(int tehsilId);

    @Query("SELECT * FROM VillageData WHERE village_id =:villageId")
    AllVillageBean.VillageData getVillage(int villageId);

    @Query("SELECT COUNT(village_id) FROM VillageData")
    int getNumberOfRows();

    @Query("DELETE FROM VillageData")
    void deleteDistrictList();
}