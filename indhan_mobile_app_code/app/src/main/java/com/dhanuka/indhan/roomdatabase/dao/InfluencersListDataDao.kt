package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.influencers.InfluencersData

@Dao
interface InfluencersListDataDao {
    @Insert
    fun insert(data: InfluencersData?): Long

    @get:Query("SELECT * FROM InfluencersData")
    val allInfluencers: List<InfluencersData?>?

    @Query("DELETE FROM InfluencersData")
    fun deleteTableData()
}