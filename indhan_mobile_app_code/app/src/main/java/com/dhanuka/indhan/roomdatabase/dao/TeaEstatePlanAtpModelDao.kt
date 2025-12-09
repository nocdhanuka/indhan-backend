package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstatePlanAtpModel

@Dao
interface TeaEstatePlanAtpModelDao {
    @Insert
    fun insert(data: TeaEstatePlanAtpModel?): Long

    @Query("SELECT * FROM TeaEstatePlanAtpModel WHERE status =:status")
    fun getAllAddPlanAtpModelData(
        status: Int
    ): List<TeaEstatePlanAtpModel>?

    @Query("SELECT * FROM TeaEstatePlanAtpModel")
    fun getAllAddPlanAtpModelData(): List<TeaEstatePlanAtpModel>?

    @Query("DELETE FROM TeaEstatePlanAtpModel")
    fun deleteAllPlanAtpModelData()
}