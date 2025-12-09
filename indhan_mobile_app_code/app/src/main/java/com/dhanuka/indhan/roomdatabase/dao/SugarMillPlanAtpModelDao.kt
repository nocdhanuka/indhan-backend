package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillPlanAtpModel

@Dao
interface SugarMillPlanAtpModelDao {
    @Insert
    fun insert(data: SugarMillPlanAtpModel?): Long

    @Query("SELECT * FROM SugarMillPlanAtpModel WHERE status =:status")
    fun getAllAddPlanAtpModelData(
        status: Int
    ): List<SugarMillPlanAtpModel>?

    @Query("SELECT * FROM SugarMillPlanAtpModel")
    fun getAllAddPlanAtpModelData(): List<SugarMillPlanAtpModel>?

    @Query("DELETE FROM SugarMillPlanAtpModel")
    fun deleteAllPlanAtpModelData()
}