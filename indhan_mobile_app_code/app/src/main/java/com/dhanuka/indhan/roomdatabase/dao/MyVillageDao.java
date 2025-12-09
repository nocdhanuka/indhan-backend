package com.dhanuka.indhan.roomdatabase.dao;

import androidx.room.Dao;
import androidx.room.Insert;
import androidx.room.OnConflictStrategy;
import androidx.room.Query;

import com.dhanuka.indhan.MyVillage.MyVillageBean;

import java.util.List;

@Dao
public interface MyVillageDao {

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    void insert(List<MyVillageBean.MyVillageData> data);

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    void insert(MyVillageBean.MyVillageData data);

    @Query("SELECT * FROM MyVillageData")
    List<MyVillageBean.MyVillageData> getMyVillageList();

    @Query("SELECT * FROM MyVillageData WHERE isFarmerData=:isFarmer LIMIT 1")
    MyVillageBean.MyVillageData getSingleVillageData(Boolean isFarmer);

    @Query("DELETE FROM MyVillageData")
    void deleteAllVillages();

    @Query("UPDATE MyVillageData SET isFarmerData = :data WHERE villageId = :id")
    void updateVillageData(boolean data, String id);

}