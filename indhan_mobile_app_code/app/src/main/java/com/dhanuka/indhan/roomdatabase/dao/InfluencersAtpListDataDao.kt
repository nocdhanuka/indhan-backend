package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.influencers.InfluencersAtpListData

@Dao
interface InfluencersAtpListDataDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: InfluencersAtpListData)

    @Query("SELECT * FROM InfluencersAtpListData")
    fun getAllAtpListData(): List<InfluencersAtpListData?>?

    @Query("SELECT * FROM InfluencersAtpListData WHERE influencer_id =:id")
    fun getAllById(id: Int): List<InfluencersAtpListData?>?

    @Query("DELETE FROM InfluencersAtpListData WHERE influencer_atp_id =:id")
    fun deleteTableData(id: Int)

}