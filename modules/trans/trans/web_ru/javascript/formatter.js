/**
***  This file Copyright (C) 2010 Mnemosyne LLC
***
***  This code is licensed under the GPL version 2.
***  For more details, see http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
**/

Transmission.fmt = (function()
{
	var speed_K = 1024;
	var speed_B_str = 'B';
	var speed_K_str = 'Кб/с';
	var speed_M_str = 'Мб/с';
	var speed_G_str = 'Гб/с';
	var speed_T_str = 'Тб/с';

	var size_K = 1024;
	var size_B_str = 'B';
	var size_K_str = 'Кб';
	var size_M_str = 'Мб';
	var size_G_str = 'Гб';
	var size_T_str = 'Тб';

	var mem_K = 1024;
	var mem_B_str = 'B';
	var mem_K_str = 'Кб';
	var mem_M_str = 'Мб';
	var mem_G_str = 'Гб';
	var mem_T_str = 'Тб';

	return {

		updateUnits: function( u )
		{
			speed_K     = u['speed-bytes'];
			speed_K_str = u['speed-units'][0];
			speed_M_str = u['speed-units'][1];
			speed_G_str = u['speed-units'][2];
			speed_T_str = u['speed-units'][3];

			size_K     = u['size-bytes'];
			size_K_str = u['size-units'][0];
			size_M_str = u['size-units'][1];
			size_G_str = u['size-units'][2];
			size_T_str = u['size-units'][3];

			mem_K     = u['memory-bytes'];
			mem_K_str = u['memory-units'][0];
			mem_M_str = u['memory-units'][1];
			mem_G_str = u['memory-units'][2];
			mem_T_str = u['memory-units'][3];
		},

		/*
		 *   Format a percentage to a string
		 */
		percentString: function( x ) {
			if( x < 10.0 )
				return x.toTruncFixed( 2 );
			else if( x < 100.0 )
				return x.toTruncFixed( 1 );
			else
				return x.toTruncFixed( 0 );
		},

		/*
		 *   Format a ratio to a string
		 */
		ratioString: function( x ) {
			if( x ==  -1 )
				return "Неизвестно";
			else if( x == -2 )
				return '&infin;';
			else
				return this.percentString( x );
		},

		/**
		 * Formats the a memory size into a human-readable string
		 * @param {Number} bytes the filesize in bytes
		 * @return {String} human-readable string
		 */
		mem: function( bytes )
		{
			if( bytes < mem_K )
				return [ bytes, mem_B_str ].join(' ');

			var convertedSize;
			var unit;

			if( bytes < Math.pow( mem_K, 2 ) )
			{
				convertedSize = bytes / mem_K;
				unit = mem_K_str;
			}
			else if( bytes < Math.pow( mem_K, 3 ) )
			{
				convertedSize = bytes / Math.pow( mem_K, 2 );
				unit = mem_M_str;
			}
			else if( bytes < Math.pow( mem_K, 4 ) )
			{
				convertedSize = bytes / Math.pow( mem_K, 3 );
				unit = mem_G_str;
			}
			else
			{
				convertedSize = bytes / Math.pow( mem_K, 4 );
				unit = mem_T_str;
			}

			// try to have at least 3 digits and at least 1 decimal
			return convertedSize <= 9.995 ? [ convertedSize.toTruncFixed(2), unit ].join(' ')
			                              : [ convertedSize.toTruncFixed(1), unit ].join(' ');
		},

		/**
		 * Formats the a disk capacity or file size into a human-readable string
		 * @param {Number} bytes the filesize in bytes
		 * @return {String} human-readable string
		 */
		size: function( bytes )
		{
			if( bytes < size_K )
				return [ bytes, size_B_str ].join(' ');

			var convertedSize;
			var unit;

			if( bytes < Math.pow( size_K, 2 ) )
			{
				convertedSize = bytes / size_K;
				unit = size_K_str;
			}
			else if( bytes < Math.pow( size_K, 3 ) )
			{
				convertedSize = bytes / Math.pow( size_K, 2 );
				unit = size_M_str;
			}
			else if( bytes < Math.pow( size_K, 4 ) )
			{
				convertedSize = bytes / Math.pow( size_K, 3 );
				unit = size_G_str;
			}
			else
			{
				convertedSize = bytes / Math.pow( size_K, 4 );
				unit = size_T_str;
			}

			// try to have at least 3 digits and at least 1 decimal
			return convertedSize <= 9.995 ? [ convertedSize.toTruncFixed(2), unit ].join(' ')
			                              : [ convertedSize.toTruncFixed(1), unit ].join(' ');
		},

		speedBps: function( Bps )
		{
			return this.speed( this.toKBps( Bps ) );
		},

		toKBps: function( Bps )
		{
			return Math.floor( Bps / speed_K );
		},

		speed: function( KBps )
		{
			var speed = KBps;

			if (speed <= 999.95) // 0 KBps to 999.9 K
				return [ speed.toTruncFixed(1), speed_K_str ].join(' ');

			speed /= speed_K;

			if (speed <= 99.995) // 1 M to 99.99 M
				return [ speed.toTruncFixed(2), speed_M_str ].join(' ');
			if (speed <= 999.95) // 100 M to 999.9 M
				return [ speed.toTruncFixed(1), speed_M_str ].join(' ');

			// insane speeds
			speed /= speed_K;
			return [ speed.toTruncFixed(2), speed_G_str ].join(' ');
		},

		timeInterval: function( seconds )
		{
			var result;
			var days = Math.floor(seconds / 86400);
			var hours = Math.floor((seconds % 86400) / 3600);
			var minutes = Math.floor((seconds % 3600) / 60);
			var seconds = Math.floor((seconds % 3600) % 60);

			if (days > 0 && hours == 0)
				result = [ days, 'день' ];
			else if (days > 0 && hours > 0)
				result = [ days, 'дней', hours, 'часов' ];
			else if (hours > 0 && minutes == 0)
				result = [ hours, 'час' ];
			else if (hours > 0 && minutes > 0)
				result = [ hours,'часов', minutes, 'минут' ];
			else if (minutes > 0 && seconds == 0)
				result = [ minutes, 'минута' ];
			else if (minutes > 0 && seconds > 0)
				result = [ minutes, 'минут', seconds, 'секунд' ];
			else
				result = [ seconds, 'секунда' ];

			return result.join(' ');
		},

		timestamp: function( seconds )
		{
			if( !seconds )
				return 'Н/Д';

		var myDate = new Date(seconds*1000);
			var now = new Date();

			var date = "";
			var time = "";

			var sameYear = now.getFullYear() == myDate.getFullYear();
			var sameMonth = now.getMonth() == myDate.getMonth();

			var dateDiff = now.getDate() - myDate.getDate();
			if(sameYear && sameMonth && Math.abs(dateDiff) <= 1){
				if(dateDiff == 0){
					date = "Сегодня";
				}
				else if(dateDiff == 1){
					date = "Вчера";
				}
				else{
					date = "Завтра";
				}
			}
			else{
				date = myDate.toDateString();
			}

			var hours = myDate.getHours();
			var period = "AM";
			if(hours > 12){
				hours = hours - 12;
				period = "PM";
			}
			if(hours == 0){
				hours = 12;
			}
			if(hours < 10){
				hours = "0" + hours;
			}
			var minutes = myDate.getMinutes();
			if(minutes < 10){
				minutes = "0" + minutes;
			}
			var seconds = myDate.getSeconds();
				if(seconds < 10){
					seconds = "0" + seconds;
			}

			time = [hours, minutes, seconds].join(':');

			return [date, time, period].join(' ');
		},

		plural: function( i, word )
		{
			return [ i, ' ', word, (word==1?'':'s') ].join('');
		}
	}
})();
