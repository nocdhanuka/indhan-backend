package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.influencers.AddInfluencersBranchModel

@Dao
interface AddInfluencersBranchDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: AddInfluencersBranchModel?): Long

    @Query("SELECT * FROM AddInfluencersBranchModel WHERE status =:status LIMIT 1")
    fun getSingleAddInfluencersBranchData(
        status: Int
    ): AddInfluencersBranchModel?

    @Query("SELECT * FROM AddInfluencersBranchModel")
    fun getAllAddInfluencersBranchData(): List<AddInfluencersBranchModel>?

    @Query("SELECT * FROM AddInfluencersBranchModel WHERE status =:status AND hitTime<=:time LIMIT 1")
    fun getSingleAddInfluencersBranchData(status: Int, time: Long): AddInfluencersBranchModel?

    @Query("SELECT * FROM AddInfluencersBranchModel WHERE hitTime<=:time LIMIT 1")
    fun getSosAddInfluencersBranchData(time: Long): AddInfluencersBranchModel?

    @Query("UPDATE AddInfluencersBranchModel SET status =:status ,hitTime =:lastHitTime WHERE influencerId=:id")
    fun updateAddInfluencersBranchData(id: Long, lastHitTime: Long, status: Int)

    @Query("DELETE FROM AddInfluencersBranchModel WHERE influencerId =:id")
    fun deleteAddInfluencersBranchData(id: Long)

}