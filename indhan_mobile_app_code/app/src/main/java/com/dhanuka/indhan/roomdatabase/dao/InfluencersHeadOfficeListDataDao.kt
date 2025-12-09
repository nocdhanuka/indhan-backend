package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.influencers.InfluencersHeadOfficeData

@Dao
interface InfluencersHeadOfficeListDataDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: InfluencersHeadOfficeData?): Long

    @get:Query("SELECT * FROM InfluencersHeadOfficeData")
    val allInfluencers: List<InfluencersHeadOfficeData?>?

    @Query("DELETE FROM InfluencersHeadOfficeData")
    fun deleteTableData()
}