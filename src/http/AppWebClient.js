import http from '@/plugins/axios'

export default class AppWebClient {

  constructor(silent = false) {
    this.http = http(silent);
  }

  /**
   * Obtenha a lista das possibilidades de estados civis que o sistema conhece.
   * @returns {Promise<array>}
   */
  async getEstadosCivis() {
    const {data} = await this.http.get('estadoscivis');
    return data;
  }
}