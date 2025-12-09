package com.dhanuka.indhan.roomdatabase.dao;

import androidx.room.Dao;
import androidx.room.Insert;
import androidx.room.OnConflictStrategy;
import androidx.room.Query;

import com.dhanuka.indhan.MenuScreenActivity.dbmodel.AllStateBean;

import java.util.List;

@Dao
public interface StateDao {

    @Insert
    void insert(List<AllStateBean.StateData> data);


    @Insert(onConflict = OnConflictStrategy.REPLACE)
    void insert(AllStateBean.StateData data);

    @Query("SELECT * FROM StateData")
    List<AllStateBean.StateData> getAllStateList();

    @Query("SELECT COUNT(state_id) FROM StateData")
    int getNumberOfRows();

    @Query("SELECT * FROM StateData WHERE state =:state")
    AllStateBean.StateData getStateIdFromName(String state);

    @Query("DELETE FROM StateData")
    void deleteStateList();
}