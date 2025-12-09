package com.dhanuka.indhan.VideoPlayer

import android.annotation.SuppressLint
import android.app.Activity
import android.net.Uri
import android.os.Bundle
import android.text.TextUtils
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import com.dhanuka.indhan.R
import com.google.android.exoplayer2.ExoPlayer
import com.google.android.exoplayer2.MediaItem
import com.google.android.exoplayer2.Player
import com.google.android.exoplayer2.SimpleExoPlayer
import com.google.android.exoplayer2.trackselection.DefaultTrackSelector
import com.google.android.exoplayer2.util.Util
import kotlinx.android.synthetic.main.activity_video_play.*

class VideoPlayActivity : AppCompatActivity() {

    private var playWhenReady = true
    private var currentWindow = 0
    private var playbackPosition = 0L
    private val playbackStateListener: Player.EventListener = playbackStateListener()
    private var player: SimpleExoPlayer? = null
    private lateinit var mActivity: Activity
    private var videoUrl = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_video_play)
        mActivity = this

        if (intent.hasExtra("VideoUrl")) {
            videoUrl = intent?.getStringExtra("VideoUrl") ?: ""
        }
        if (Util.SDK_INT >= 24) {
            initializePlayer()
        }
        closeVideo.setOnClickListener {
            finish()
        }
    }

    private fun playbackStateListener() = object : Player.EventListener {
        override fun onPlaybackStateChanged(playbackState: Int) {
            if (playbackState == ExoPlayer.STATE_BUFFERING) {
                pbProgress.visibility = View.VISIBLE
            } else {
                pbProgress.visibility = View.GONE
            }
        }
    }

    @SuppressLint("InlinedApi")
    private fun hideSystemUi() {
        exo_player_view.systemUiVisibility = (View.SYSTEM_UI_FLAG_LOW_PROFILE
                or View.SYSTEM_UI_FLAG_FULLSCREEN
                or View.SYSTEM_UI_FLAG_LAYOUT_STABLE
                or View.SYSTEM_UI_FLAG_IMMERSIVE_STICKY
                or View.SYSTEM_UI_FLAG_LAYOUT_HIDE_NAVIGATION
                or View.SYSTEM_UI_FLAG_HIDE_NAVIGATION)
    }

    public override fun onPause() {
        super.onPause()
        if (Util.SDK_INT < 24) {
            releasePlayer()
        }
    }

    public override fun onStop() {
        super.onStop()
        if (Util.SDK_INT >= 24) {
            releasePlayer()
        }
    }

    public override fun onResume() {
        super.onResume()
        hideSystemUi()
        if ((Util.SDK_INT < 24 || player == null)) {
            initializePlayer()
        }
    }

    private fun initializePlayer() {
        val trackSelector = DefaultTrackSelector(this).apply {
            setParameters(buildUponParameters().setMaxVideoSizeSd())
        }
        player = SimpleExoPlayer.Builder(this)
            .setTrackSelector(trackSelector)
            .build()
            .also { exoPlayer ->
                exo_player_view.player = exoPlayer
                exoPlayer.seekTo(currentWindow, playbackPosition)
                exoPlayer.addListener(playbackStateListener)
            }
        if (player != null) {
            playExo()
        }
    }

    private fun releasePlayer() {
        player?.run {
            playbackPosition = this.currentPosition
            currentWindow = this.currentWindowIndex
            playWhenReady = this.playWhenReady
            removeListener(playbackStateListener)
            release()
        }
        player = null
    }

    /*
      Play video in exo player
    */
    fun playExo() {
        if (!TextUtils.isEmpty(videoUrl) && player != null) {
            val videoURI = Uri.parse(videoUrl)
            val mediaItem = MediaItem.fromUri(videoURI)
            player?.setMediaItem(mediaItem)
            player?.playWhenReady = playWhenReady
            player?.seekTo(currentWindow, playbackPosition)
            player?.prepare()
        }

    }

    override fun onDestroy() {
        super.onDestroy()
        releasePlayer()
    }
}
