package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.influencers.InfluencersCategoryData

@Dao
interface InfluencersCategoryDataDao {
    @Insert
    fun insert(data: InfluencersCategoryData?): Long

    @get:Query("SELECT * FROM InfluencersCategoryData")
    val allCategoryData: List<InfluencersCategoryData?>?

    @Query("DELETE FROM InfluencersCategoryData")
    fun deleteTableData()
}