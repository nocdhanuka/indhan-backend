package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.influencers.InfluencersPlanAtpModel

@Dao
interface InfluencersPlanAtpModelDao {
    @Insert
    fun insert(data: InfluencersPlanAtpModel?): Long

    @Query("SELECT * FROM InfluencersPlanAtpModel WHERE status =:status")
    fun getAllAddPlanAtpModelData(
        status: Int
    ): List<InfluencersPlanAtpModel>?

    @Query("SELECT * FROM InfluencersPlanAtpModel")
    fun getAllAddPlanAtpModelData(): List<InfluencersPlanAtpModel>?

    @Query("DELETE FROM InfluencersPlanAtpModel")
    fun deleteAllPlanAtpModelData()
}